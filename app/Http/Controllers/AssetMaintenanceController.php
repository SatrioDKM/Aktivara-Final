<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetsMaintenance;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AssetMaintenanceController extends Controller
{
    /**
     * Menampilkan halaman daftar riwayat maintenance (index).
     *
     * @return View
     */
    public function viewPage(): View
    {
        $data = [
            'technicians' => User::whereIn('role_id', ['TK01', 'TK02'])->orderBy('name')->get(['id', 'name']),
        ];
        return view('backend.master.maintenances.index', compact('data'));
    }

    /**
     * Menampilkan halaman formulir untuk melaporkan kerusakan aset.
     *
     * @return View
     */
    public function create(): View
    {
        $data = [
            'assets' => Asset::where('asset_type', 'fixed_asset')->where('status', 'available')->orderBy('name_asset')->get(),
        ];
        return view('backend.master.maintenances.create', compact('data'));
    }

    /**
     * Menampilkan halaman detail maintenance.
     *
     * @param string $id
     * @return View
     */
    public function showPage(string $id): View
    {
        $data = [
            // PERBAIKAN: Menggunakan nama relasi 'task' yang benar
            'maintenance' => AssetsMaintenance::with(['asset.room.floor.building', 'technician:id,name', 'task'])->findOrFail($id)
        ];
        return view('backend.master.maintenances.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir untuk mengedit/memperbarui status maintenance.
     *
     * @param string $id
     * @return View
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
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = AssetsMaintenance::with(['asset', 'technician:id,name']);

        $query->when($request->input('search'), function ($q, $search) {
            $q->where('description', 'like', "%{$search}%")
                ->orWhereHas('asset', fn($assetQuery) => $assetQuery->where('name_asset', 'like', "%{$search}%"));
        });

        $query->when($request->input('status'), fn($q, $status) => $q->where('status', $status));
        $query->when($request->input('technician'), fn($q, $techId) => $q->where('user_id', $techId));

        $maintenances = $query->latest()->paginate($request->input('perPage', 10));
        return response()->json($maintenances);
    }

    /**
     * API: Menyimpan laporan kerusakan baru dan otomatis membuat tugas perbaikan.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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
            $maintenance = DB::transaction(function () use ($request) {
                $asset = Asset::find($request->input('asset_id'));
                $asset->update(['status' => 'maintenance']);

                $newMaintenance = AssetsMaintenance::create([
                    'asset_id' => $asset->id,
                    'maintenance_type' => $request->input('maintenance_type'),
                    'description' => $request->input('description'),
                    'start_date' => $request->input('start_date') ?: now(),
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
                    'priority' => $request->input('priority'),
                    'description' => "Laporan Kerusakan: " . $request->input('description'),
                    'created_by' => Auth::id(),
                    'status' => 'unassigned',
                ]);

                return $newMaintenance;
            });

            return response()->json($maintenance->load(['asset', 'technician']), 210);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: Menampilkan satu data maintenance spesifik.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $maintenance = AssetsMaintenance::with(['asset', 'technician:id,name'])->findOrFail($id);
        return response()->json($maintenance);
    }

    /**
     * API: Memperbarui data status maintenance.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $maintenance = AssetsMaintenance::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Otomatis tugaskan teknisi jika status diubah menjadi 'in_progress' atau 'completed'
        if (in_array($data['status'], ['in_progress', 'completed']) && empty($data['user_id'])) {
            $data['user_id'] = $maintenance->user_id ?: Auth::id();
        }

        $maintenance->update($data);

        // Update status aset terkait berdasarkan status maintenance
        if ($data['status'] === 'completed') {
            $maintenance->asset->update(['status' => 'available', 'condition' => 'Baik']);
        } elseif (in_array($data['status'], ['cancelled', 'scheduled'])) {
            // Jika maintenance dibatalkan atau dijadwalkan ulang, aset kembali available
            $maintenance->asset->update(['status' => 'available']);
        }

        return response()->json($maintenance->load(['asset', 'technician']));
    }

    /**
     * API: Menghapus data maintenance.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $maintenance = AssetsMaintenance::findOrFail($id);

        // Kembalikan status aset menjadi 'available' jika maintenance dihapus
        if ($maintenance->asset && $maintenance->asset->status === 'maintenance') {
            $maintenance->asset->update(['status' => 'available']);
        }

        $maintenance->delete();

        return response()->json(['message' => 'Data maintenance berhasil dihapus.'], 200);
    }
}
