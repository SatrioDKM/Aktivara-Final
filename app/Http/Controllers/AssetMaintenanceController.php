<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetsMaintenance;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AssetMaintenanceController extends Controller
{
    /**
     * Menampilkan halaman utama (index).
     */
    public function viewPage(): View
    {
        $data = [
            'assets' => Asset::where('asset_type', 'fixed_asset')->where('status', '!=', 'disposed')->orderBy('name_asset')->get(),
            'technicians' => User::whereIn('role_id', ['TK01', 'TK02'])->orderBy('name')->get(['id', 'name']),
        ];
        return view('backend.master.maintenances.index', compact('data'));
    }

    /**
     * Menampilkan halaman formulir tambah.
     */
    public function create(): View
    {
        $data = [
            'assets' => Asset::where('asset_type', 'fixed_asset')->where('status', 'available')->orderBy('name_asset')->get(),
        ];
        return view('backend.master.maintenances.create', compact('data'));
    }

    /**
     * Menampilkan halaman detail (show).
     */
    public function showPage(string $id): View
    {
        $data = [
            'maintenance' => AssetsMaintenance::with(['asset.room.floor.building', 'technician:id,name', 'generatedTask:id,assets_maintenance_id'])->findOrFail($id)
        ];
        return view('backend.master.maintenances.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir edit.
     */
    public function edit(string $id): View
    {
        $data = [
            'maintenance' => AssetsMaintenance::with('asset')->findOrFail($id),
            'technicians' => User::whereIn('role_id', ['TK01', 'TK02'])->orderBy('name')->get(['id', 'name']),
        ];
        return view('backend.master.maintenances.edit', compact('data'));
    }

    // ===================================================================
    // API METHODS
    // ===================================================================

    /**
     * API: Menampilkan daftar riwayat maintenance dengan paginasi dan filter.
     */
    public function index()
    {
        $query = AssetsMaintenance::with(['asset', 'technician:id,name']);

        if (request('search', '')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('asset', fn($assetQuery) => $assetQuery->where('name_asset', 'like', "%{$search}%"));
            });
        }

        if (request('status', '')) {
            $query->where('status', request('status'));
        }

        if (request('technician', '')) {
            $query->where('user_id', request('technician'));
        }

        $maintenances = $query->latest()->paginate(request('perPage', 10));
        return response()->json($maintenances);
    }

    /**
     * API: Menyimpan laporan kerusakan baru dan memicu pembuatan tugas.
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'asset_id' => 'required|exists:assets,id,asset_type,fixed_asset',
            'maintenance_type' => 'required|in:repair,routine_check,replacement',
            'description' => 'required|string|min:10',
            'start_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $maintenance = DB::transaction(function () {
                $asset = Asset::find(request('asset_id'));
                $asset->update(['status' => 'maintenance']);

                $newMaintenance = AssetsMaintenance::create([
                    'asset_id' => $asset->id,
                    'maintenance_type' => request('maintenance_type'),
                    'description' => request('description'),
                    'start_date' => request('start_date') ?: now(),
                    'status' => 'scheduled',
                ]);

                $taskType = TaskType::firstOrCreate(
                    ['name_task' => 'Perbaikan Aset', 'departemen' => 'TK'],
                    ['description' => 'Tugas perbaikan aset yang dilaporkan rusak.', 'priority_level' => 'high']
                );

                Task::create([
                    'title' => "Perbaikan Aset: " . $asset->name_asset,
                    'task_type_id' => $taskType->id,
                    'assets_maintenance_id' => $newMaintenance->id,
                    'asset_id' => $asset->id,
                    'room_id' => $asset->room_id,
                    'priority' => request('priority'),
                    'description' => "Laporan Kerusakan: " . request('description'),
                    'created_by' => Auth::id(),
                    'status' => 'unassigned',
                ]);

                return $newMaintenance;
            });

            return response()->json($maintenance->load(['asset', 'technician']), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: Menampilkan satu data maintenance spesifik.
     */
    public function show(string $id)
    {
        $maintenance = AssetsMaintenance::with(['asset', 'technician:id,name'])->findOrFail($id);
        return response()->json($maintenance);
    }

    /**
     * API: Memperbarui data maintenance.
     */
    public function update(string $id)
    {
        $maintenance = AssetsMaintenance::findOrFail($id);

        $validator = Validator::make(request()->all(), [
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if (in_array($data['status'], ['in_progress', 'completed']) && empty($data['user_id'])) {
            $data['user_id'] = $maintenance->user_id ?: Auth::id();
        }

        $maintenance->update($data);

        if ($data['status'] === 'completed') {
            $maintenance->asset->update(['status' => 'available', 'condition' => 'Baik']);
        } elseif ($data['status'] === 'cancelled') {
            $maintenance->asset->update(['status' => 'available']);
        }

        return response()->json($maintenance->load(['asset', 'technician']));
    }

    /**
     * API: Menghapus data maintenance.
     */
    public function destroy(string $id)
    {
        $maintenance = AssetsMaintenance::findOrFail($id);
        if ($maintenance->asset && $maintenance->asset->status === 'maintenance') {
            $maintenance->asset->update(['status' => 'available']);
        }
        $maintenance->delete();
        return response()->json(null, 204);
    }
}
