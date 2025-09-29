<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Room;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AssetController extends Controller
{
    /**
     * Menampilkan halaman utama (index).
     */
    public function viewPage(): View
    {
        $data = [
            'rooms' => Room::with('floor.building')->where('status', 'active')->get(),
        ];
        return view('backend.master.assets.index', compact('data'));
    }

    /**
     * Menampilkan halaman formulir tambah.
     */
    public function create(): View
    {
        $data = [
            'rooms' => Room::with('floor.building')->where('status', 'active')->get(),
        ];
        return view('backend.master.assets.create', compact('data'));
    }

    /**
     * Menampilkan halaman detail (show).
     */
    public function showPage(string $id): View
    {
        $data = [
            'asset' => Asset::with(['room.floor.building', 'updater:id,name', 'creator:id,name', 'maintenances.technician:id,name', 'tasks.staff:id,name'])->findOrFail($id)
        ];
        return view('backend.master.assets.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir edit.
     */
    public function edit(string $id): View
    {
        $data = [
            'asset' => Asset::findOrFail($id),
            'rooms' => Room::with('floor.building')->where('status', 'active')->get(),
        ];
        return view('backend.master.assets.edit', compact('data'));
    }

    // ===================================================================
    // API METHODS
    // ===================================================================

    /**
     * API: Menampilkan daftar aset dengan paginasi dan filter.
     */
    public function index()
    {
        $query = Asset::with(['room.floor.building', 'creator:id,name']);

        // Filter Tipe Aset (Tab)
        if (request('asset_type', '')) {
            $query->where('asset_type', request('asset_type'));
        }

        // Filter Pencarian
        if (request('search', '')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name_asset', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $assets = $query->latest()->paginate(request('perPage', 10));

        return response()->json($assets);
    }

    /**
     * API: Menyimpan data aset baru (bisa lebih dari satu).
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'assets' => 'required|array|min:1',
            'assets.*.name_asset' => 'required|string|max:100',
            'assets.*.asset_type' => 'required|in:fixed_asset,consumable',
            'assets.*.category' => 'required|string|max:100', // Diperbarui untuk nama kategori
            'assets.*.room_id' => 'nullable|exists:rooms,id',
            'assets.*.purchase_date' => 'nullable|date',
            'assets.*.current_stock' => 'required|integer|min:1',
            'assets.*.minimum_stock' => 'nullable|integer|min:0',
            'assets.*.description' => 'nullable|string',
            'assets.*.condition' => 'required_if:assets.*.asset_type,fixed_asset|in:Baik,Rusak Ringan,Rusak Berat',
        ], [
            'assets.*.name_asset.required' => 'Nama aset di baris #:position wajib diisi.',
            'assets.*.category.required' => 'Kategori di baris #:position wajib diisi.',
            'assets.*.current_stock.min' => 'Stok di baris #:position minimal 1.',
            'assets.*.condition.required_if' => 'Kondisi untuk Aset Tetap di baris #:position wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $createdAssets = [];
        DB::transaction(function () use (&$createdAssets) {
            foreach (request('assets') as $assetData) {
                $data = $assetData;
                $data['created_by'] = Auth::id();
                $data['updated_by'] = Auth::id();

                if ($data['asset_type'] === 'fixed_asset') {
                    $data['status'] = 'available';
                    for ($i = 0; $i < $data['current_stock']; $i++) {
                        $singleAssetData = $data;
                        $singleAssetData['current_stock'] = 1;
                        $singleAssetData['minimum_stock'] = 0;
                        $singleAssetData['serial_number'] = $this->generateSerialNumber($data['category']);
                        $createdAssets[] = Asset::create($singleAssetData);
                    }
                } else {
                    $data['status'] = 'available';
                    $data['condition'] = 'Baik';
                    $asset = Asset::create($data);
                    $this->checkAndNotifyLowStock($asset);
                    $createdAssets[] = $asset;
                }
            }
        });

        return response()->json(['message' => 'Aset berhasil ditambahkan!', 'assets' => $createdAssets], 201);
    }

    /**
     * API: Menampilkan satu data aset spesifik.
     */
    public function show(string $id)
    {
        $asset = Asset::with(['room.floor.building', 'updater:id,name'])->findOrFail($id);
        return response()->json($asset);
    }

    /**
     * API: Memperbarui data aset yang sudah ada.
     */
    public function update(string $id)
    {
        $asset = Asset::findOrFail($id);

        $validator = Validator::make(request()->all(), [
            'name_asset' => 'required|string|max:100',
            'room_id' => 'nullable|exists:rooms,id',
            'category' => 'required|string|max:100',
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number,' . $asset->id,
            'purchase_date' => 'nullable|date',
            'condition' => 'required_if:asset_type,fixed_asset|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|in:available,in_use,maintenance,disposed',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'asset_type' => 'required|in:fixed_asset,consumable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['updated_by'] = Auth::id();
        unset($data['asset_type']);

        $asset->update($data);
        $this->checkAndNotifyLowStock($asset);

        return response()->json($asset->load(['room.floor.building', 'updater:id,name']));
    }

    /**
     * API: Menghapus data aset.
     */
    public function destroy(string $id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
        return response()->json(null, 204);
    }

    /**
     * API: Mengurangi stok untuk barang habis pakai.
     */
    public function stockOut(string $id)
    {
        $validator = Validator::make(request()->all(), ['amount' => 'required|integer|min:1']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $asset = Asset::findOrFail($id);

        if ($asset->asset_type !== 'consumable') {
            return response()->json(['message' => 'Hanya barang habis pakai yang bisa dikurangi stoknya.'], 400);
        }
        if ($asset->current_stock < request('amount')) {
            return response()->json(['message' => 'Stok tidak mencukupi.'], 422);
        }

        $asset->decrement('current_stock', request('amount'));
        $asset->updated_by = Auth::id();
        $asset->save();

        $this->checkAndNotifyLowStock($asset);

        return response()->json($asset);
    }

    /**
     * Helper: Memeriksa stok dan mengirim notifikasi.
     */
    private function checkAndNotifyLowStock(Asset $asset)
    {
        if ($asset->asset_type === 'consumable' && $asset->minimum_stock > 0 && $asset->current_stock <= $asset->minimum_stock) {
            $recipients = User::whereIn('role_id', ['SA00', 'MG00', 'WH01', 'WH02'])->get();
            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new LowStockAlert($asset));
            }
        }
    }

    /**
     * Helper: Membuat nomor seri unik.
     */
    private function generateSerialNumber(string $categoryName): string
    {
        $categoryCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $categoryName), 0, 3));
        $prefix = $categoryCode . date('dmy');
        $lastAsset = Asset::where('serial_number', 'like', $prefix . '%')->orderBy('serial_number', 'desc')->first();
        $nextNumber = $lastAsset ? ((int) substr($lastAsset->serial_number, -4)) + 1 : 1;
        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
