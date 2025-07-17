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
            $tasksCreated = Task::where('created_by', $user->id);

            // Statistik aset yang relevan untuk departemen Leader
            $departmentCode = substr($roleId, 0, 2);
            $departmentAssets = Asset::where('serial_number', 'like', $departmentCode . '-%')
                ->orWhereHas('tasks.taskType', function ($query) use ($departmentCode) {
                    $query->where('departemen', $departmentCode);
                });

            $stats = [
                'role_type' => 'leader',
                'tasks_created_total' => $tasksCreated->count(),
                'tasks_pending_review' => (clone $tasksCreated)->where('status', 'pending_review')->count(),
                'tasks_in_progress_by_team' => (clone $tasksCreated)->where('status', 'in_progress')->count(),
                'tasks_completed_by_team' => (clone $tasksCreated)->where('status', 'completed')->count(),
                'department_assets_count' => $departmentAssets->count(), // Statistik aset baru untuk leader
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
