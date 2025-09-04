<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\PackingList;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PackingListController extends Controller
{
    /**
     * Menampilkan halaman utama Barang Keluar.
     */
    public function viewPage()
    {
        // Ambil daftar aset yang 'available' untuk dipilih
        $assets = Asset::where('status', 'available')->orderBy('name_asset')->get();
        // Ambil riwayat packing list yang sudah dibuat
        $packingLists = PackingList::with('creator:id,name', 'assets')->latest()->paginate(10);

        return view('packing_lists.index', compact('assets', 'packingLists'));
    }

    /**
     * Menyimpan data packing list baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'asset_ids' => 'required|array|min:1',
            'asset_ids.*' => 'exists:assets,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Buat nomor dokumen unik
                $documentNumber = 'PL-' . date('Ymd') . '-' . str_pad(PackingList::count() + 1, 4, '0', STR_PAD_LEFT);

                // 1. Simpan data packing list
                $packingList = PackingList::create([
                    'document_number' => $documentNumber,
                    'recipient_name' => $request->recipient_name,
                    'notes' => $request->notes,
                    'created_by' => Auth::id(),
                ]);

                // 2. Tautkan aset yang dipilih ke packing list
                $packingList->assets()->attach($request->asset_ids);

                // 3. Ubah status aset yang keluar menjadi 'in_use'
                Asset::whereIn('id', $request->asset_ids)->update(['status' => 'in_use']);
            });

            return redirect()->back()->with('success', 'Packing list berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Mengekspor packing list ke PDF.
     */
    public function exportPdf(PackingList $packingList)
    {
        // Muat relasi yang dibutuhkan
        $packingList->load('creator', 'assets');

        // Buat PDF dari view
        $pdf = Pdf::loadView('pdf.packing_list', ['packingList' => $packingList]);

        // Tampilkan atau download PDF
        return $pdf->stream($packingList->document_number . '.pdf');
    }
}
