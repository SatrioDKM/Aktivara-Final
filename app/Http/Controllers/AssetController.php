<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class AssetController extends Controller
{
    /**
     * Method untuk menampilkan halaman Blade.
     */
    public function viewPage()
    {
        $rooms = Room::with('floor.building')->where('status', 'active')->get();
        return view('master.assets.index', compact('rooms'));
    }

    /**
     * Menampilkan daftar aset berdasarkan peran.
     */
    public function index()
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        $query = Asset::with([
            'room.floor.building',
            'updater:id,name',
            'creator:id,name',
            'maintenances.technician:id,name',
            'tasks.staff:id,name'
        ]);

        if (str_ends_with($roleId, '01')) {
            $departmentCode = substr($roleId, 0, 2);
            $query->where('serial_number', 'like', $departmentCode . '-%');
        }

        $assets = $query->latest()->get();
        return response()->json($assets);
    }

    /**
     * Menyimpan data aset baru.
     * **(REVISI: LOGIKA department_code DIHAPUS)**
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        $validator = Validator::make($request->all(), [
            'name_asset' => 'required|string|max:100',
            'asset_type' => 'required|in:fixed_asset,consumable',
            'room_id' => 'nullable|exists:rooms,id',
            'category' => 'required|string|max:50',
            'purchase_date' => 'nullable|date',
            'condition' => 'required_if:asset_type,fixed_asset|string|max:50',
            'status' => 'required|in:available,in_use,maintenance,disposed',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required_if:asset_type,consumable|integer|min:0',
            'description' => 'nullable|string',
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated(); // Mengambil data yang sudah divalidasi
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        // Logika untuk Aset Tetap
        if ($data['asset_type'] === 'fixed_asset') {
            $data['minimum_stock'] = 0;
            $data['current_stock'] = 1;

            // Nomor seri otomatis HANYA dibuat oleh Leader
            if (str_ends_with($roleId, '01')) {
                $departmentCode = substr($roleId, 0, 2);
                // Jika serial number tidak diisi manual, buat otomatis
                if (empty($data['serial_number'])) {
                    $data['serial_number'] = $this->generateSerialNumber($departmentCode);
                }
            }
            // Admin/Manager harus mengisi nomor seri secara manual jika diperlukan
        }

        $asset = Asset::create($data);
        $this->checkAndNotifyLowStock($asset);

        return response()->json($asset->load(['room.floor.building', 'updater:id,name', 'creator:id,name', 'maintenances', 'tasks']), 201);
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
     */
    public function update(Request $request, string $id)
    {
        $asset = Asset::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_asset' => 'required|string|max:100',
            'room_id' => 'nullable|exists:rooms,id',
            'category' => 'required|string|max:50',
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number,' . $asset->id,
            'purchase_date' => 'nullable|date',
            'condition' => 'required_if:asset_type,fixed_asset|string|max:50',
            'status' => 'required|in:available,in_use,maintenance,disposed',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required_if:asset_type,consumable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['updated_by'] = Auth::id();

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
     */
    private function checkAndNotifyLowStock(Asset $asset)
    {
        if ($asset->asset_type === 'consumable' && $asset->current_stock <= $asset->minimum_stock && $asset->minimum_stock > 0) {
            $recipients = User::whereIn('role_id', ['SA00', 'MG00'])
                ->orWhere('role_id', 'like', '%01')
                ->get()
                ->unique('id');

            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new LowStockAlert($asset));
            }
        }
    }

    /**
     * Helper method untuk membuat nomor seri unik.
     */
    private function generateSerialNumber(string $departmentCode): string
    {
        $lastAsset = Asset::where('serial_number', 'like', $departmentCode . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $number = 1;
        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->serial_number, strpos($lastAsset->serial_number, '-') + 1);
            $number = $lastNumber + 1;
        }

        return $departmentCode . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
