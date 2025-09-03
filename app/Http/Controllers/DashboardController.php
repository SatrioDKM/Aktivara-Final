<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DailyReport;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman Blade untuk Dashboard.
     */
    public function viewPage()
    {
        return view('dashboard');
    }

    /**
     * Endpoint API untuk mengambil data statistik agregat berdasarkan peran.
     * (LOGIKA DIPERBARUI)
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();
        $roleId = $user->role_id;
        $stats = [];

        // --- Dashboard untuk Manager & Superadmin ---
        if (in_array($roleId, ['SA00', 'MG00'])) {
            $taskStats = Task::query()->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');

            // --- STATISTIK ASET DIPERBARUI ---
            // Pisahkan query untuk Aset Tetap dan Barang Habis Pakai
            $fixedAssetsQuery = Asset::where('asset_type', 'fixed_asset');
            $consumableAssetsQuery = Asset::where('asset_type', 'consumable');

            // Hitung aset yang stoknya di bawah minimum
            $lowStockAssets = (clone $consumableAssetsQuery)
                ->whereRaw('current_stock <= minimum_stock')
                ->where('minimum_stock', '>', 0)
                ->count();

            $latestReports = DailyReport::with(['user:id,name', 'task:id,title'])
                ->latest()
                ->take(10)
                ->get();

            $stats = [
                'role_type' => 'admin',
                'total_users' => User::count(),
                'tasks' => [
                    'unassigned' => $taskStats->get('unassigned', 0),
                    'in_progress' => $taskStats->get('in_progress', 0),
                    'pending_review' => $taskStats->get('pending_review', 0),
                    'completed' => $taskStats->get('completed', 0),
                ],
                'assets' => [
                    'total_fixed' => (clone $fixedAssetsQuery)->count(),
                    'total_consumable' => (clone $consumableAssetsQuery)->count(),
                    'fixed_in_maintenance' => (clone $fixedAssetsQuery)->where('status', 'maintenance')->count(),
                    'consumable_low_stock' => $lowStockAssets,
                ],
                'latest_reports' => $latestReports,
            ];
        }
        // --- Dashboard untuk Leader ---
        else if (str_ends_with($roleId, '01')) {
            // Ambil daftar staff di departemen Leader untuk filter dropdown
            $departmentCode = substr($roleId, 0, 2);
            $staffInDepartment = User::where('role_id', $departmentCode . '02')->orderBy('name')->get(['id', 'name']);

            // Query dasar: semua tugas yang dibuat oleh Leader ini
            $query = Task::with(['staff:id,name', 'taskType:id,name_task'])
                ->where('created_by', $user->id);

            // Terapkan filter berdasarkan status
            $query->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'dikerjakan') {
                    return $q->where('status', 'in_progress');
                }
                return $q->where('status', $request->status);
            });

            // Terapkan filter berdasarkan staff
            $query->when($request->filled('staff_id'), fn($q) => $q->where('user_id', $request->staff_id));

            // Terapkan filter berdasarkan tanggal
            $query->when($request->filled('start_date'), fn($q) => $q->whereDate('created_at', '>=', $request->start_date));
            $query->when($request->filled('end_date'), fn($q) => $q->whereDate('created_at', '<=', $request->end_date));

            // Terapkan filter pencarian
            $query->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });

            $tasks = $query->latest('updated_at')->paginate(10);

            $stats = [
                'role_type' => 'leader',
                'tasks' => $tasks,
                'staff_list' => $staffInDepartment,
            ];
        }
        // --- Dashboard untuk Staff ---
        else {
            $userDepartment = substr($roleId, 0, 2);

            // Query untuk tugas tersedia tidak berubah, sudah benar.
            $availableTasksQuery = Task::with(['creator:id,name', 'room.floor.building', 'taskType'])
                ->where('status', 'unassigned')
                ->whereHas('taskType', function ($q) use ($userDepartment) {
                    $q->where('departemen', $userDepartment)->orWhere('departemen', 'UMUM');
                });

            $availableTasksQuery->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });

            $availableTasks = $availableTasksQuery->latest()->paginate(5);

            $myTasks = Task::where('user_id', $user->id);

            $stats = [
                'role_type' => 'staff',
                'available_tasks' => $availableTasks,
                'my_active_tasks_count' => (clone $myTasks)->whereIn('status', ['in_progress', 'rejected'])->count(),
                'my_completed_tasks_count' => (clone $myTasks)->where('status', 'completed')->count(),
            ];
        }

        return response()->json($stats);
    }
}
