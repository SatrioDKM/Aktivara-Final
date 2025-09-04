<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class AssetController extends Controller
{
    /**
     * Menampilkan halaman Blade.
     */
    public function viewPage()
    {
        $rooms = Room::with('floor.building')->where('status', 'active')->get();
        return view('master.assets.index', compact('rooms'));
    }

    /**
     * Menampilkan daftar aset.
     */
    public function index()
    {
        $assets = Asset::with(['room.floor.building', 'updater:id,name', 'creator:id,name', 'maintenances.technician:id,name', 'tasks.staff:id,name'])
            ->latest()
            ->get();
        return response()->json($assets);
    }

    /**
     * Menyimpan data aset baru (bisa lebih dari satu).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assets' => 'required|array|min:1',
            'assets.*.name_asset' => 'required|string|max:100',
            'assets.*.asset_type' => 'required|in:fixed_asset,consumable',
            'assets.*.category' => 'required|string|max:3',
            'assets.*.room_id' => 'nullable|exists:rooms,id',
            'assets.*.purchase_date' => 'nullable|date',
            'assets.*.current_stock' => 'required|integer|min:1',
            'assets.*.minimum_stock' => 'nullable|integer|min:0',
            'assets.*.description' => 'nullable|string',
            'assets.*.condition' => 'required_if:assets.*.asset_type,fixed_asset|in:Baik,Rusak Ringan,Rusak Berat',
        ], [
            'assets.*.name_asset.required' => 'Nama aset di baris #:position wajib diisi.',
            'assets.*.category.required' => 'Kategori di baris #:position wajib diisi.',
            'assets.*.category.max' => 'Kode Kategori di baris #:position maksimal 3 karakter.',
            'assets.*.current_stock.min' => 'Stok di baris #:position minimal 1.',
            'assets.*.condition.required_if' => 'Kondisi untuk Aset Tetap di baris #:position wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $createdAssets = [];
        DB::transaction(function () use ($request, &$createdAssets) {
            foreach ($request->assets as $assetData) {
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
     * Menampilkan satu data aset spesifik.
     */
    public function show(string $id)
    {
        $asset = Asset::with(['room.floor.building', 'updater:id,name'])->findOrFail($id);
        return response()->json($asset);
    }

    /**
     * Memperbarui data aset yang sudah ada.
     * (REVISI TOTAL)
     */
    public function update(Request $request, string $id)
    {
        $asset = Asset::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_asset' => 'required|string|max:100',
            'room_id' => 'nullable|exists:rooms,id',
            'category' => 'required|string|max:100', // Disesuaikan dengan form
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number,' . $asset->id,
            'purchase_date' => 'nullable|date',
            'condition' => 'required_if:asset_type,fixed_asset|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|in:available,in_use,maintenance,disposed',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'asset_type' => 'required|in:fixed_asset,consumable', // Sertakan untuk validasi
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ambil hanya data yang sudah divalidasi
        $data = $validator->validated();
        $data['updated_by'] = Auth::id();

        // Jangan izinkan perubahan tipe aset setelah dibuat
        unset($data['asset_type']);

        $asset->update($data);

        $this->checkAndNotifyLowStock($asset);

        return response()->json($asset->load(['room.floor.building', 'updater:id,name']));
    }

    /**
     * Menghapus data aset.
     */
    public function destroy(string $id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
        return response()->json(null, 204);
    }

    /**
     * Mengurangi stok untuk barang habis pakai.
     */
    public function stockOut(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), ['amount' => 'required|integer|min:1']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $asset = Asset::findOrFail($id);

        if ($asset->asset_type !== 'consumable') {
            return response()->json(['message' => 'Hanya barang habis pakai yang bisa dikurangi stoknya.'], 400);
        }

        if ($asset->current_stock < $request->amount) {
            return response()->json(['message' => 'Stok tidak mencukupi.'], 422);
        }

        $asset->current_stock -= $request->amount;
        $asset->updated_by = Auth::id();
        $asset->save();

        $this->checkAndNotifyLowStock($asset);

        return response()->json($asset);
    }

    /**
     * Helper method untuk memeriksa stok dan mengirim notifikasi.
     * (LOGIKA DIPERBARUI)
     */
    private function checkAndNotifyLowStock(Asset $asset)
    {
        if ($asset->asset_type === 'consumable' && $asset->current_stock <= $asset->minimum_stock && $asset->minimum_stock > 0) {
            // Kirim notifikasi HANYA ke Warehouse, Superadmin, dan Manager
            $recipients = User::whereIn('role_id', ['SA00', 'MG00', 'WH01', 'WH02'])
                ->get();

            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new LowStockAlert($asset));
            }
        }
    }

    /**
     * Helper method untuk membuat nomor seri unik.
     */
    private function generateSerialNumber(string $categoryCode): string
    {
        $categoryCode = strtoupper(substr($categoryCode, 0, 3));
        $datePart = date('dmy');
        $prefix = $categoryCode . $datePart;

        $lastAsset = Asset::where('serial_number', 'like', $prefix . '%')
            ->orderBy('serial_number', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastAsset) {
            $lastSequence = (int) substr($lastAsset->serial_number, -4);
            $nextNumber = $lastSequence + 1;
        }

        $sequencePart = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return $prefix . $sequencePart;
    }
}
