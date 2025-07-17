<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Asset;
use App\Models\TaskType;
use Illuminate\Http\Request;
use App\Models\AssetsMaintenance;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AssetMaintenanceController extends Controller
{
    /**
     * Menampilkan halaman Blade untuk Maintenance Aset.
     */
    public function viewPage()
    {
        // Ambil hanya aset tetap, karena hanya itu yang bisa di-maintenance
        $assets = Asset::where('asset_type', 'fixed_asset')
            ->where('status', '!=', 'disposed')
            ->orderBy('name_asset')
            ->get();

        return view('maintenance.index', compact('assets'));
    }

    /**
     * Menampilkan daftar semua riwayat maintenance.
     */
    public function index()
    {
        // Pastikan relasi technician dimuat
        $maintenances = AssetsMaintenance::with(['asset', 'technician:id,name'])->latest()->get();
        return response()->json($maintenances);
    }

    /**
     * Menyimpan laporan kerusakan baru dan memicu pembuatan tugas.
     * **(REVISI)**
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => 'required|exists:assets,id,asset_type,fixed_asset', // Pastikan hanya Aset Tetap
            'maintenance_type' => 'required|in:repair,routine_check,replacement',
            'description_text' => 'required|string|min:10',
            'start_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $maintenance = DB::transaction(function () use ($request) {
                $asset = Asset::find($request->asset_id);

                // 1. Buat record maintenance
                $newMaintenance = AssetsMaintenance::create([
                    'asset_id' => $asset->id,
                    'maintenance_type' => $request->maintenance_type,
                    'description_text' => $request->description_text,
                    'start_date' => $request->start_date,
                    'status' => 'scheduled', // Status awal
                ]);

                // 2. Ubah status aset menjadi 'maintenance'
                $asset->update(['status' => 'maintenance']);

                // 3. Cari atau buat TaskType untuk perbaikan
                $taskType = TaskType::firstOrCreate(
                    ['name_task' => 'Perbaikan Aset', 'departemen' => 'TK'],
                    ['description' => 'Tugas perbaikan aset yang dilaporkan rusak.']
                );

                // 4. Buat Task baru dan tautkan dengan maintenance_id
                Task::create([
                    'title' => "Perbaikan Aset: " . $asset->name_asset,
                    'task_type_id' => $taskType->id,
                    'assets_maintenance_id' => $newMaintenance->id, // <-- TAUTAN DIBUAT DI SINI
                    'asset_id' => $asset->id,
                    'room_id' => $asset->room_id,
                    'priority' => 'high', // Tambahkan prioritas
                    'description' => "Laporan Kerusakan: " . $request->description_text,
                    'created_by' => Auth::id(),
                    'status' => 'unassigned', // Dilempar ke job pool Teknisi
                ]);

                return $newMaintenance;
            });

            return response()->json($maintenance->load(['asset', 'technician']), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal membuat laporan maintenance: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Memperbarui data maintenance.
     * **(REVISI)**
     */
    public function update(Request $request, string $id)
    {
        $maintenance = AssetsMaintenance::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Tetapkan teknisi yang mengerjakan hanya jika belum ada & statusnya relevan
        if (!$maintenance->user_id && in_array($data['status'], ['in_progress', 'completed'])) {
            $data['user_id'] = Auth::id();
        }

        $maintenance->update($data);

        // Jika maintenance selesai, kembalikan status aset menjadi 'available'
        if ($data['status'] === 'completed') {
            $maintenance->asset->update(['status' => 'available', 'condition' => 'Baik']);
        }
        // Jika dibatalkan, kembalikan status aset juga
        else if ($data['status'] === 'cancelled') {
            $maintenance->asset->update(['status' => 'available']);
        }

        return response()->json($maintenance->load(['asset', 'technician']));
    }

    /**
     * Menghapus data maintenance.
     */
    public function destroy(string $id)
    {
        $maintenance = AssetsMaintenance::findOrFail($id);

        // Kembalikan status aset sebelum menghapus record
        if ($maintenance->asset->status === 'maintenance') {
            $maintenance->asset->update(['status' => 'available']);
        }

        $maintenance->delete();

        return response()->json(null, 204);
    }
}
