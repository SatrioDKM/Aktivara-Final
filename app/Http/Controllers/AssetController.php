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
        // Ambil data ruangan untuk dropdown di form
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

        // Eager load relasi yang dibutuhkan untuk tampilan history dan data lainnya
        $query = Asset::with([
            'room.floor.building',
            'updater:id,name',
            'creator:id,name',
            'maintenances.technician:id,name', // History maintenance
            'tasks.staff:id,name' // History pemakaian/pergerakan
        ]);

        // Jika user adalah Leader, filter aset berdasarkan kode departemen di nomor seri.
        if (str_ends_with($roleId, '01')) {
            $departmentCode = substr($roleId, 0, 2); // Ambil kode departemen (e.g., 'HK')
            $query->where('serial_number', 'like', $departmentCode . '-%');
        }
        // Admin/Manager akan melihat semua aset (tidak perlu filter tambahan).

        $assets = $query->latest()->get();
        return response()->json($assets);
    }

    /**
     * Menyimpan data aset baru dengan nomor seri otomatis.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        $validator = Validator::make($request->all(), [
            'name_asset' => 'required|string|max:100',
            'asset_type' => 'required|in:fixed_asset,consumable', // Validasi tipe aset
            'room_id' => 'nullable|exists:rooms,id',
            'category' => 'required|string|max:50',
            'purchase_date' => 'nullable|date',
            'condition' => 'required_if:asset_type,fixed_asset|string|max:50', // Wajib untuk aset tetap
            'status' => 'required|in:available,in_use,maintenance,disposed',
            'current_stock' => 'required|integer|min:0',
            // Stok minimum hanya wajib jika tipenya consumable
            'minimum_stock' => 'required_if:asset_type,consumable|integer|min:0',
            'description' => 'nullable|string',
            // Validasi department_code hanya jika user adalah Admin/Manager
            'department_code' => [
                Rule::requiredIf(fn() => in_array($roleId, ['SA00', 'MG00'])),
                'in:HK,TK,SC,PK'
            ],
            // Nomor seri tidak perlu divalidasi karena dibuat otomatis
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->except('serial_number');
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        // Jika Aset Tetap, set minimum_stock ke 0 dan current_stock ke 1 (by default)
        if ($data['asset_type'] === 'fixed_asset') {
            $data['minimum_stock'] = 0;
            $data['current_stock'] = 1;
        }

        // --- Logika Pembuatan Nomor Seri Otomatis ---
        $departmentCode = '';
        if (in_array($roleId, ['SA00', 'MG00'])) {
            $departmentCode = $request->department_code;
        } else if (str_ends_with($roleId, '01')) {
            $departmentCode = substr($roleId, 0, 2);
        }

        // Generate nomor seri hanya jika ada kode departemen
        if ($departmentCode) {
            $data['serial_number'] = $this->generateSerialNumber($departmentCode);
        }
        // ---------------------------------------------

        $asset = Asset::create($data);
        $this->checkAndNotifyLowStock($asset);

        // Muat relasi baru untuk dikirim kembali
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
            'condition' => 'required|string|max:50',
            'status' => 'required|in:available,in_use,maintenance,disposed',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $asset->update($data);

        // --- PEMICU NOTIFIKASI STOK MENIPIS (SAAT UPDATE) ---
        $this->checkAndNotifyLowStock($asset);
        // ----------------------------------------------------

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
     * Helper method untuk memeriksa stok dan mengirim notifikasi.
     * (INI METODE YANG DIPERBARUI)
     */
    private function checkAndNotifyLowStock(Asset $asset)
    {
        // Kirim notifikasi hanya jika stok saat ini di bawah atau sama dengan batas minimum,
        // dan batas minimum tersebut lebih dari 0.
        if ($asset->current_stock <= $asset->minimum_stock && $asset->minimum_stock > 0) {

            // Ambil semua Manager dan Superadmin
            $managersAndAdmins = User::whereIn('role_id', ['SA00', 'MG00'])->get();

            // Ambil semua Leader (role_id diakhiri dengan '01')
            $leaders = User::where('role_id', 'like', '%01')->get();

            // Gabungkan semua penerima dan pastikan tidak ada duplikat
            $recipients = $managersAndAdmins->merge($leaders)->unique('id');

            // Kirim notifikasi jika ada penerima
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
        // Cari aset terakhir dengan prefix yang sama untuk mendapatkan nomor urut berikutnya
        $lastAsset = Asset::where('serial_number', 'like', $departmentCode . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $number = 1;
        if ($lastAsset) {
            // Ambil bagian numerik setelah tanda '-'
            $lastNumber = (int) substr($lastAsset->serial_number, strpos($lastAsset->serial_number, '-') + 1);
            $number = $lastNumber + 1;
        }

        // Format nomor dengan padding nol (e.g., HK-000001)
        return $departmentCode . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Mengurangi stok untuk barang habis pakai.
     * (INI METHOD BARU)
     */
    public function stockOut(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $asset = Asset::findOrFail($id);

        // Pastikan hanya barang habis pakai yang bisa dikurangi stoknya
        if ($asset->asset_type !== 'consumable') {
            return response()->json(['message' => 'Hanya barang habis pakai yang bisa dikurangi stoknya.'], 400);
        }

        if ($asset->current_stock < $request->amount) {
            return response()->json(['message' => 'Stok tidak mencukupi.'], 422);
        }

        // Kurangi stok
        $asset->current_stock -= $request->amount;
        $asset->updated_by = Auth::id();
        $asset->save();

        // Cek apakah stok sekarang berada di bawah minimum
        $this->checkAndNotifyLowStock($asset);

        return response()->json($asset);
    }
}
