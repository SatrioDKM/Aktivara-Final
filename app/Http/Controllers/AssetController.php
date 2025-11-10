<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Room;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function viewPage(): View
    {
        return view('backend.master.assets.index');
    }

    /**
     * Menampilkan halaman formulir untuk menambah aset baru.
     */
    public function create()
    {
        $rooms = Room::all();
        $categories = AssetCategory::orderBy('name')->get(); // Ambil kategori
        return view('backend.master.assets.create', compact('rooms', 'categories')); // Tambahkan 'categories'
    }

    /**
     * Menampilkan halaman detail aset.
     */
    public function showPage(string $id): View
    {
        // PERBAIKAN: Mengganti relasi 'tasks.staff' menjadi 'tasks.assignee'
        $data = [
            'asset' => Asset::with([
                'room.floor.building',
                'updater:id,name',
                'creator:id,name',
                'maintenances.technician:id,name',
                'tasks.assignee:id,name',
                'category',
                'movements.fromRoom', // Eager load movements and their rooms
                'movements.toRoom',
                'movements.movedBy',
            ])->findOrFail($id)
        ];
        return view('backend.master.assets.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir untuk mengedit aset.
     */
    public function edit(string $id): View
    {
        $data = [
            'asset' => Asset::findOrFail($id),
            'rooms' => Room::with('floor.building')->where('status', 'active')->get(),
            'categories' => AssetCategory::orderBy('name')->get(), // <-- TAMBAHKAN INI
        ];
        return view('backend.master.assets.edit', compact('data'));
    }

    // ===================================================================
    // API METHODS
    // ===================================================================

    /**
     * API: Menampilkan daftar aset dengan paginasi dan filter.
     *
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'perPage' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
            'asset_type' => 'required|in:fixed_asset,consumable',
        ]);

        $search = $request->input('search');
        $assetType = $request->input('asset_type');
        $perPage = $request->input('perPage', 10);

        if ($assetType == 'consumable') {

            // --- Alur 1: Barang Habis Pakai (Tidak Berubah) ---
            $query = Asset::query()
                ->with(['room', 'category'])
                ->where('asset_type', 'consumable')
                ->when($search, function ($q, $search) {
                    $q->where('name_asset', 'like', '%' . $search . '%')
                        ->orWhereHas('category', fn($qc) => $qc->where('name', 'like', '%' . $search . '%'));
                })
                ->latest();

            return response()->json($query->paginate($perPage));
        } else {

            // --- Alur 2: Aset Tetap (FIXED: Menggunakan groupBy) ---

            $query = Asset::query()
                ->with('category') // Wajib Eager Load
                ->where('asset_type', 'fixed_asset')
                ->when($search, function ($q, $search) {
                    $q->where('name_asset', 'like', '%' . $search . '%')
                        ->orWhere('serial_number', 'like', '%' . $search . '%')
                        ->orWhereHas('category', fn($qc) => $qc->where('name', 'like', '%' . $search . '%'));
                });

            $allAssets = $query->get();

            // Kelompokkan berdasarkan nama kategori.
            // Aset dengan category_id=NULL akan masuk ke grup 'Tanpa Kategori'.
            $grouped = $allAssets->groupBy(function ($asset) {
                return $asset->category->name ?? 'Tanpa Kategori';
            });

            // Ubah formatnya agar sesuai dengan yg diharapkan frontend
            $categorySummary = $grouped->map(function ($assets, $categoryName) {
                // Tentukan ID. Jika 'Tanpa Kategori', kita beri ID '0'.
                $id = ($categoryName == 'Tanpa Kategori') ? 0 : $assets->first()->asset_category_id;

                return [
                    'id' => $id, // ID Kategori (atau 0 jika null)
                    'name' => $categoryName,
                    'assets_count' => $assets->count()
                ];
            })->sortBy('name')->values(); // Urutkan A-Z dan reset keys

            return response()->json($categorySummary);
        }
    }

    /**
     * API: Mengambil daftar semua aset yang tersedia atau sedang digunakan untuk dropdown.
     *
     * @return JsonResponse
     */
    public function listAllForDropdown(): JsonResponse
    {
        $assets = Asset::whereIn('status', ['available', 'in_use'])
            ->orderBy('name_asset')
            ->get(['id', 'name_asset', 'serial_number', 'asset_type', 'current_stock']); // Tambahkan 'current_stock'

        return response()->json($assets);
    }

    /**
     * API: Menyimpan data aset baru (bisa lebih dari satu).
     *     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'assets' => 'required|array|min:1',
            'assets.*.name_asset' => 'required|string|max:100',
            'assets.*.asset_type' => 'required|in:fixed_asset,consumable',
            'assets.*.asset_category_id' => 'required|exists:asset_categories,id',
            'assets.*.room_id' => 'nullable|exists:rooms,id',
            'assets.*.location_detail' => 'nullable|string|max:255', // Add this line
            'assets.*.purchase_date' => 'nullable|date',
            'assets.*.current_stock' => 'required|integer|min:1',
            'assets.*.minimum_stock' => 'nullable|integer|min:0',
            'assets.*.description' => 'nullable|string',
            'assets.*.condition' => 'required_if:assets.*.asset_type,fixed_asset|nullable|in:Baik,Rusak Ringan,Rusak Berat',
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
        DB::transaction(function () use ($request, &$createdAssets) {
            foreach ($request->input('assets') as $assetData) {
                $data = $assetData;
                $data['created_by'] = Auth::id();
                $data['updated_by'] = Auth::id();

                if ($data['asset_type'] === 'fixed_asset') {
                    $data['status'] = 'available';
                    for ($i = 0; $i < $data['current_stock']; $i++) {
                        $singleAssetData = $data;
                        $singleAssetData['current_stock'] = 1;
                        $singleAssetData['minimum_stock'] = 0;
                        $singleAssetData['location_detail'] = $data['location_detail'] ?? null; // Add this line
                        // Ambil nama kategori dari ID
                        $categoryName = AssetCategory::find($data['asset_category_id'])->name;
                        $singleAssetData['serial_number'] = $this->generateSerialNumber($categoryName);
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
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $asset = Asset::with(['room.floor.building', 'updater:id,name'])->findOrFail($id);
        return response()->json($asset);
    }

    /**
     * API: Memperbarui data aset yang sudah ada.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $asset = Asset::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_asset' => 'required|string|max:100',
            'room_id' => 'nullable|exists:rooms,id',
            'location_detail' => 'nullable|string|max:255', // Add this line
            'asset_category_id' => 'required|exists:asset_categories,id',
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number,' . $asset->id,
            'purchase_date' => 'nullable|date',
            'condition' => 'required_if:asset_type,fixed_asset|nullable|in:Baik,Rusak Ringan,Rusak Berat',
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
        unset($data['asset_type']); // Mencegah perubahan tipe aset setelah dibuat

        $asset->update($data);

        // --- LOGIKA BARU: Log pergerakan aset jika room_id berubah ---
        if ($asset->room_id !== $oldRoomId) {
            AssetMovement::create([
                'asset_id' => $asset->id,
                'from_room_id' => $oldRoomId,
                'to_room_id' => $asset->room_id,
                'moved_by_user_id' => Auth::id(),
                'description' => 'Perpindahan aset melalui update manual.',
            ]);
        }
        // --- AKHIR LOGIKA BARU ---

        $this->checkAndNotifyLowStock($asset);

        return response()->json($asset->load(['room.floor.building', 'updater:id,name']));
    }

    /**
     * API: Menghapus data aset.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
        return response()->json(['message' => 'Aset berhasil dihapus.'], 200);
    }

    public function showByCategory($categoryId) // Request gak perlu kalau gak dipakai
    {
        // Tangani ID 0 (Tanpa Kategori)
        if ($categoryId == 0) {
            $category = (object)[
                'id' => 0,
                'name' => 'Tanpa Kategori'
            ];
        } else {
            // Gunakan findOrFail biar otomatis 404 kalau nggak ketemu
            $category = AssetCategory::findOrFail($categoryId);
        }

        // Kirim data kategori ke view
        return view('backend.master.assets.category_detail', compact('category'));
    }

    public function apiShowByCategory(Request $request, $categoryId): JsonResponse // <-- METHOD BARU
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'perPage' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
        ]);

        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);

        $query = Asset::query()
            ->with(['room'])
            ->where('asset_type', 'fixed_asset')
            ->when($search, function ($q, $search) {
                $q->where('name_asset', 'like', '%' . $search . '%')
                    ->orWhere('serial_number', 'like', '%' . $search . '%');
            });

        // Logika untuk handle ID 0 (Tanpa Kategori)
        if ($categoryId == 0) {
            $query->whereNull('asset_category_id');
        } else {
            $query->where('asset_category_id', $categoryId);
        }

        $assets = $query->latest()->paginate($perPage);

        return response()->json($assets);
    }

    /**
     * API: Mengurangi stok untuk barang habis pakai.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function stockOut(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), ['amount' => 'required|integer|min:1']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $asset = Asset::findOrFail($id);

        if ($asset->asset_type !== 'consumable') {
            return response()->json(['message' => 'Hanya barang habis pakai yang bisa dikurangi stoknya.'], 400);
        }
        if ($asset->current_stock < $request->input('amount')) {
            return response()->json(['message' => 'Stok tidak mencukupi.'], 422);
        }

        $asset->decrement('current_stock', $request->input('amount'));
        $asset->updated_by = Auth::id();
        $asset->save();

        $this->checkAndNotifyLowStock($asset);

        return response()->json($asset);
    }

    /**
     * Helper privat untuk memeriksa stok dan mengirim notifikasi jika stok menipis.
     *
     * @param Asset $asset
     * @return void
     */
    private function checkAndNotifyLowStock(Asset $asset): void
    {
        if ($asset->asset_type === 'consumable' && $asset->minimum_stock > 0 && $asset->current_stock <= $asset->minimum_stock) {
            $recipients = User::whereIn('role_id', ['SA00', 'MG00', 'WH01', 'WH02'])->get();
            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new LowStockAlert($asset));
            }
        }
    }

    /**
     * Helper privat untuk membuat nomor seri unik berdasarkan kategori dan tanggal.
     *
     * @param string $categoryName
     * @return string
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
