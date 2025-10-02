<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Asset;
use App\Models\DailyReport;
use App\Models\PackingList;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman Blade untuk Dashboard.
     */
    public function viewPage(): View
    {
        $data = []; // Variabel data dikirim meski kosong untuk konsistensi
        return view('backend.dashboard', compact('data'));
    }

    /**
     * Endpoint API untuk mengambil data statistik agregat berdasarkan peran.
     */
    public function getStats(Request $request): JsonResponse
    {
        $user = Auth::user();
        $roleId = $user->role_id;
        $data = [];

        // --- Dashboard untuk Manager & Superadmin ---
        if (in_array($roleId, ['SA00', 'MG00'])) {
            // ... (logika untuk admin tidak berubah, sudah benar) ...
            $startDate = $request->input('start_date', Carbon::now()->subMonth()->toDateString());
            $endDate = $request->input('end_date', Carbon::now()->toDateString());
            $assetsIn = Asset::whereBetween('created_at', [$startDate, $endDate])->count();
            $assetsOut = DB::table('asset_packing_list')
                ->join('packing_lists', 'asset_packing_list.packing_list_id', '=', 'packing_lists.id')
                ->whereBetween('packing_lists.created_at', [$startDate, $endDate])
                ->count();
            $taskStats = Task::query()->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
            $fixedAssetsQuery = Asset::where('asset_type', 'fixed_asset');
            $consumableAssetsQuery = Asset::where('asset_type', 'consumable');
            $lowStockAssets = (clone $consumableAssetsQuery)->whereRaw('current_stock <= minimum_stock')->where('minimum_stock', '>', 0)->count();

            $data = [
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
                'asset_movement' => [
                    'in' => $assetsIn,
                    'out' => $assetsOut,
                ],
            ];
        }
        // --- Dashboard untuk Leader ---
        else if (str_ends_with($roleId, '01')) {
            $departmentCode = substr($roleId, 0, 2);
            $staffInDepartment = User::where('role_id', $departmentCode . '02')->orderBy('name')->get(['id', 'name']);

            // =======================================================
            // --- PERBAIKAN DI SINI: Ganti 'staff' menjadi 'assignee' ---
            // =======================================================
            $query = Task::with(['assignee:id,name', 'taskType:id,name_task'])
                ->where('created_by', $user->id);

            $query->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'dikerjakan') {
                    return $q->where('status', 'in_progress');
                }
                return $q->where('status', $request->status);
            });

            $query->when($request->filled('staff_id'), fn($q) => $q->where('user_id', $request->staff_id));
            $query->when($request->filled('start_date'), fn($q) => $q->whereDate('created_at', '>=', $request->start_date));
            $query->when($request->filled('end_date'), fn($q) => $q->whereDate('created_at', '<=', $request->end_date));

            $query->when($request->input('search.value'), function ($q) use ($request) {
                $searchTerm = '%' . $request->input('search.value') . '%';
                $q->where('title', 'like', $searchTerm);
            });

            $tasks = $query->latest('updated_at')->paginate($request->input('length', 10));

            $data = [
                'role_type' => 'leader',
                'tasks' => $tasks,
                'staff_list' => $staffInDepartment,
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $tasks->total(),
                'recordsFiltered' => $tasks->total(),
                'data' => $tasks->items(),
            ];
        }
        // --- Dashboard untuk Staff ---
        else {
            // ... (logika untuk staff tidak berubah, sudah benar) ...
            $userDepartment = substr($roleId, 0, 2);
            $query = Task::with(['creator:id,name', 'room.floor.building'])
                ->where('status', 'unassigned')
                ->whereHas('taskType', fn($q) => $q->where('departemen', $userDepartment)->orWhere('departemen', 'UMUM'));
            $query->when($request->filled('search'), function ($q) use ($request) {
                $searchTerm = '%' . $request->search . '%';
                $q->where('title', 'like', $searchTerm);
            });
            $availableTasks = $query->latest()->paginate(5);
            $myTasks = Task::where('user_id', $user->id);

            $data = [
                'role_type' => 'staff',
                'available_tasks' => $availableTasks,
                'my_active_tasks_count' => (clone $myTasks)->whereIn('status', ['in_progress', 'rejected'])->count(),
                'my_completed_tasks_count' => (clone $myTasks)->where('status', 'completed')->count(),
            ];
        }

        return response()->json($data);
    }
}
