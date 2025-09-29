<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\PackingList;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\Response;

class PackingListController extends Controller
{
    /**
     * Menampilkan halaman utama Barang Keluar.
     */
    public function viewPage(): View
    {
        $data = []; // Data akan diambil oleh API
        return view('backend.packing_lists.index', compact('data'));
    }

    /**
     * Mengekspor packing list ke PDF.
     */
    public function exportPdf(string $id): Response
    {
        $packingList = PackingList::with('creator', 'assets')->findOrFail($id);
        $pdf = Pdf::loadView('pdf.packing_list', ['packingList' => $packingList]);
        return $pdf->stream($packingList->document_number . '.pdf');
    }

    // ===================================================================
    // API METHODS
    // ===================================================================

    /**
     * API: Mengambil riwayat packing list dengan paginasi.
     */
    public function index()
    {
        $query = PackingList::with('creator:id,name')->withCount('assets');

        if (request('search', '')) {
            $search = request('search');
            $query->where('document_number', 'like', "%{$search}%")
                ->orWhere('recipient_name', 'like', "%{$search}%");
        }

        $packingLists = $query->latest()->paginate(request('perPage', 10));
        return response()->json($packingLists);
    }

    /**
     * API: Menyimpan data packing list baru.
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'recipient_name' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'asset_ids' => 'required|array|min:1',
            'asset_ids.*' => 'exists:assets,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () {
                $prefix = 'PL-' . date('Ymd') . '-';
                $lastEntry = PackingList::where('document_number', 'like', "{$prefix}%")->orderBy('document_number', 'desc')->first();
                $nextNumber = $lastEntry ? ((int) substr($lastEntry->document_number, -4)) + 1 : 1;
                $documentNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                $packingList = PackingList::create([
                    'document_number' => $documentNumber,
                    'recipient_name' => request('recipient_name'),
                    'notes' => request('notes'),
                    'created_by' => Auth::id(),
                ]);

                $packingList->assets()->attach(request('asset_ids'));

                $assets = Asset::find(request('asset_ids'));
                foreach ($assets as $asset) {
                    if ($asset->asset_type == 'fixed_asset') {
                        $asset->update(['status' => 'in_use']);
                    } else {
                        $asset->decrement('current_stock');
                    }
                }
            });

            return response()->json(['message' => 'Packing list berhasil dibuat!'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: Mengambil daftar aset yang tersedia untuk Select2.
     */
    public function getAvailableAssets()
    {
        $search = request('q');

        $assets = Asset::where('status', 'available')
            ->where(function ($query) use ($search) {
                $query->where('name_asset', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            })
            ->orderBy('name_asset')
            ->limit(20)
            ->get(['id', 'name_asset', 'serial_number', 'asset_type', 'current_stock']);

        $formattedAssets = $assets->map(function ($asset) {
            $text = $asset->name_asset;
            if ($asset->asset_type == 'fixed_asset') {
                $text .= ' (S/N: ' . ($asset->serial_number ?? 'N/A') . ')';
            } else {
                $text .= ' (Stok: ' . $asset->current_stock . ')';
            }
            return ['id' => $asset->id, 'text' => $text];
        });

        return response()->json(['results' => $formattedAssets]);
    }
}
