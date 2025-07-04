<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetsMaintenance;
use App\Models\Task;
use App\Models\TaskType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AssetMaintenanceController extends Controller
{
    /**
     * Menampilkan halaman Blade untuk Maintenance Aset.
     */
    public function viewPage()
    {
        // Ambil data aset untuk dropdown di form
        $assets = Asset::where('status', '!=', 'disposed')->orderBy('name_asset')->get();
        return view('maintenance.index', compact('assets'));
    }

    /**
     * Menampilkan daftar semua riwayat maintenance.
     */
    public function index()
    {
        $maintenances = AssetsMaintenance::with(['asset', 'technician:id,name'])->latest()->get();
        return response()->json($maintenances);
    }

    /**
     * Menyimpan laporan kerusakan baru dan memicu pembuatan tugas.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => 'required|exists:assets,id',
            'maintenance_type' => 'required|in:repair,routine_check,replacement',
            'description_text' => 'required|string',
            'start_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $maintenance = DB::transaction(function () use ($request) {
                // 1. Buat record maintenance
                $newMaintenance = AssetsMaintenance::create([
                    'asset_id' => $request->asset_id,
                    'maintenance_type' => $request->maintenance_type,
                    'description_text' => $request->description_text,
                    'start_date' => $request->start_date,
                    'status' => 'scheduled', // Status awal
                ]);

                $asset = Asset::find($request->asset_id);
                // 2. Ubah status aset menjadi 'maintenance'
                $asset->update(['status' => 'maintenance']);

                // 3. Cari atau buat TaskType untuk perbaikan
                $taskType = TaskType::firstOrCreate(
                    ['name_task' => 'Perbaikan Aset', 'departemen' => 'TK'],
                    ['priority_level' => 'high', 'description' => 'Tugas perbaikan aset yang dilaporkan rusak.']
                );

                // 4. Buat Task baru untuk departemen Teknisi
                Task::create([
                    'title' => "Perbaikan Aset: " . $asset->name_asset,
                    'task_type_id' => $taskType->id,
                    'asset_id' => $asset->id,
                    'room_id' => $asset->room_id,
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
     * Memperbarui data maintenance (misal: oleh Teknisi setelah selesai).
     */
    public function update(Request $request, string $id)
    {
        $maintenance = AssetsMaintenance::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        // Tetapkan teknisi yang mengerjakan saat status diubah
        if (in_array($request->status, ['in_progress', 'completed'])) {
            $data['user_id'] = Auth::id();
        }

        $maintenance->update($data);

        // Jika maintenance selesai, kembalikan status aset menjadi 'available'
        if ($request->status === 'completed') {
            $maintenance->asset->update(['status' => 'available', 'condition' => 'Baik']);
        } else if ($request->status === 'cancelled') {
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
        $maintenance->asset->update(['status' => 'available']);
        $maintenance->delete();

        return response()->json(null, 204);
    }
}
