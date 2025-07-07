<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DailyReport; // <-- Tambahkan ini

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
     */
    public function getStats()
    {
        $user = Auth::user();
        $roleId = $user->role_id;
        $stats = [];

        // --- Dashboard untuk Manager & Superadmin ---
        if (in_array($roleId, ['SA00', 'MG00'])) {
            $taskStats = Task::query()->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
            $assetStats = Asset::query()->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');

            // --- DATA BARU: Mengambil 10 laporan harian terbaru ---
            $latestReports = DailyReport::with([
                'task:id,title', // Hanya ambil ID dan judul dari tugas terkait
                'user:id,name'   // Hanya ambil ID dan nama dari pelapor
            ])
                ->latest() // Urutkan dari yang paling baru
                ->take(10)   // Batasi hanya 10 data
                ->get();

            $stats = [
                'role_type' => 'admin',
                'total_users' => User::count(),
                'total_assets' => $assetStats->sum(),
                'tasks' => [
                    'unassigned' => $taskStats->get('unassigned', 0),
                    'in_progress' => $taskStats->get('in_progress', 0),
                    'pending_review' => $taskStats->get('pending_review', 0),
                    'completed' => $taskStats->get('completed', 0),
                ],
                'assets' => [
                    'available' => $assetStats->get('available', 0),
                    'in_use' => $assetStats->get('in_use', 0),
                    'maintenance' => $assetStats->get('maintenance', 0),
                    'disposed' => $assetStats->get('disposed', 0),
                ],
                'latest_reports' => $latestReports, // <-- Menambahkan data laporan ke respons API
            ];
        }
        // --- Dashboard untuk Leader ---
        else if (str_ends_with($roleId, '01')) {
            $tasksCreated = Task::where('created_by', $user->id);

            $stats = [
                'role_type' => 'leader',
                'tasks_created_total' => $tasksCreated->count(),
                'tasks_pending_review' => (clone $tasksCreated)->where('status', 'pending_review')->count(),
                'tasks_in_progress_by_team' => (clone $tasksCreated)->where('status', 'in_progress')->count(),
                'tasks_completed_by_team' => (clone $tasksCreated)->where('status', 'completed')->count(),
            ];
        }
        // --- Dashboard untuk Staff ---
        else {
            $userDepartment = substr($roleId, 0, 2);
            $availableTasksCount = Task::where('status', 'unassigned')
                ->whereHas('taskType', fn($q) => $q->where('departemen', $userDepartment))
                ->count();

            $myTasks = Task::where('user_id', $user->id);

            $stats = [
                'role_type' => 'staff',
                'available_tasks_count' => $availableTasksCount,
                'my_active_tasks_count' => (clone $myTasks)->whereIn('status', ['in_progress', 'rejected'])->count(),
                'my_completed_tasks_count' => (clone $myTasks)->where('status', 'completed')->count(),
            ];
        }

        return response()->json($stats);
    }
}
