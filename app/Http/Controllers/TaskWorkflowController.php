<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Task;
use App\Models\User;
use App\Models\Asset;
use App\Models\Floor;
use App\Models\Building;
use App\Models\TaskType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Notifications\TaskClaimed;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Notifications\TaskReviewed;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewTaskAvailable;
use Illuminate\Support\Facades\Notification;

class TaskWorkflowController extends Controller
{
    // ===================================================================
    // METODE UNTUK MENAMPILKAN HALAMAN (VIEW) - Dipanggil dari web.php
    // ===================================================================

    /**
     * Menampilkan halaman Blade dengan form untuk membuat tugas.
     * (INI YANG DIPERBARUI)
     */
    public function createPage()
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        // Ambil TaskType berdasarkan peran pengguna
        $taskTypesQuery = TaskType::query();

        if (str_ends_with($roleId, '01')) { // Jika dia adalah Leader
            $departmentCode = substr($roleId, 0, 2);
            // Ambil task type yang departemennya sesuai atau yang bersifat UMUM
            $taskTypesQuery->where('departemen', $departmentCode)
                ->orWhere('departemen', 'UMUM');
        }
        // Jika Admin/Manager, tidak perlu filter, ambil semua.

        $taskTypes = $taskTypesQuery->orderBy('name_task')->get();

        // Data untuk dropdown lokasi dan aset (tidak berubah)
        $buildings = Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']);
        $floors = Floor::with('building:id,name_building')->where('status', 'active')->get(['id', 'name_floor', 'building_id']);
        $rooms = Room::with('floor:id,name_floor,building_id')->where('status', 'active')->get(['id', 'name_room', 'floor_id']);
        $assets = Asset::where('status', 'available')->orderBy('name_asset')->get();

        // Kirim semua data yang dibutuhkan ke view
        return view('tasks.create', compact('taskTypes', 'buildings', 'floors', 'rooms', 'assets'));
    }

    public function reviewPage()
    {
        return view('tasks.review_list');
    }

    public function availablePage()
    {
        return view('tasks.available');
    }

    public function myTasksPage()
    {
        return view('tasks.my_tasks');
    }

    public function showPage(Task $task)
    {
        $this->authorizeTaskAccess($task);
        $task->load(['taskType', 'room.floor.building', 'asset', 'creator', 'staff', 'dailyReports.user', 'dailyReports.attachments']);
        return view('tasks.show', compact('task'));
    }

    /**
     * Menampilkan halaman Riwayat Tugas dengan data untuk filter.
     * (INI YANG DIPERBARUI)
     */
    public function historyPage()
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        // Ambil daftar Staff
        $staffQuery = User::whereIn('role_id', ['HK02', 'TK02', 'SC02', 'PK02']);
        // Jika user adalah Leader, hanya tampilkan staff di departemennya
        if (str_ends_with($roleId, '01')) {
            $departmentCode = substr($roleId, 0, 2);
            $staffQuery->where('role_id', $departmentCode . '02');
        }
        $staffUsers = $staffQuery->orderBy('name')->get(['id', 'name']);

        // Ambil daftar departemen, hanya untuk Manager & Admin
        $departments = [];
        if (in_array($roleId, ['SA00', 'MG00'])) {
            $departments = TaskType::whereNotNull('departemen')
                ->distinct()
                ->pluck('departemen');
        }

        return view('history.tasks', compact('staffUsers', 'departments'));
    }

    public function completedHistoryPage()
    {
        return view('tasks.completed_history');
    }

    public function monitoringPage()
    {
        return view('tasks.monitoring');
    }

    public function myHistoryPage()
    {
        return view('tasks.my_history');
    }

    // ===================================================================
    // METODE UNTUK ENDPOINT API (JSON) - Dipanggil dari api.php
    // ===================================================================

    /**
     * Endpoint API untuk menyimpan tugas baru.
     * (INI YANG DIPERBARUI)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high,critical',
            'task_type_id' => 'required|exists:task_types,id', // task_type_id sekarang wajib
            'description' => 'nullable|string',
            'room_id' => 'nullable|exists:rooms,id',
            'asset_id' => 'nullable|exists:assets,id',
            'due_date' => 'nullable|date',
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'task_type_id' => $validated['task_type_id'], // Simpan dari pilihan dropdown
            'priority' => $validated['priority'],
            'description' => $validated['description'] ?? null,
            'room_id' => $validated['room_id'] ?? null,
            'asset_id' => $validated['asset_id'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'created_by' => Auth::id(),
            'user_id' => null, // Benar, karena tugas belum diambil
            'status' => 'unassigned',
            // Kolom 'department_code' tidak lagi diisi dari sini
        ]);

        // Muat relasi yang dibutuhkan, termasuk taskType
        $task->load(['creator', 'taskType']);

        // --- KIRIM NOTIFIKASI TUGAS BARU (LOGIKA BARU) ---
        $departmentCode = $task->taskType->departemen; // Ambil departemen dari relasi
        if ($departmentCode && $departmentCode !== 'UMUM') {
            $staffRole = $departmentCode . '02';
            $staffUsers = User::where('role_id', $staffRole)->get();
            if ($staffUsers->isNotEmpty()) {
                Notification::send($staffUsers, new NewTaskAvailable($task));
            }
        }
        // ------------------------------------

        // UBAH DI SINI: Tambahkan 'redirect_url' ke dalam respons JSON
        return response()->json([
            'message' => 'Tugas berhasil dibuat! Anda akan dialihkan.',
            'task' => $task,
            'redirect_url' => route('dashboard')
        ], 201);
    }

    public function claimTask(Task $task)
    {
        try {
            $claimedTask = DB::transaction(function () use ($task) {
                $taskToClaim = Task::where('id', $task->id)->where('status', 'unassigned')->lockForUpdate()->firstOrFail();
                $taskToClaim->update(['user_id' => Auth::id(), 'status' => 'in_progress']);
                $task->creator->notify(new TaskClaimed($task, Auth::user()));
                return $taskToClaim;
            });
            return response()->json(['message' => 'Tugas berhasil diambil!', 'task' => $claimedTask]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengambil tugas. Mungkin sudah diambil staff lain.'], 409);
        }
    }

    /**
     * Menampilkan daftar tugas yang tersedia untuk staff.
     * (INI YANG DIPERBARUI)
     */
    public function showAvailable()
    {
        $userDepartment = substr(Auth::user()->role_id, 0, 2);

        // Menggunakan whereHas untuk memfilter berdasarkan relasi taskType
        $availableTasks = Task::with(['room.floor.building', 'creator:id,name', 'taskType'])
            ->where('status', 'unassigned')
            ->whereHas('taskType', function ($query) use ($userDepartment) {
                $query->where('departemen', $userDepartment)
                    ->orWhere('departemen', 'UMUM');
            })
            ->latest()
            ->get();

        return response()->json($availableTasks);
    }

    public function myTasks()
    {
        $user = Auth::user();
        $myTasks = Task::with(['taskType', 'room.floor.building'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['in_progress', 'rejected'])
            ->latest()->get();
        return response()->json($myTasks);
    }

    public function show(Task $task)
    {
        $this->authorizeTaskAccess($task);
        $task->load(['taskType', 'room.floor.building', 'asset', 'creator', 'staff', 'dailyReports.user', 'dailyReports.attachments']);
        return response()->json($task);
    }

    public function showReviewList()
    {
        $tasksToReview = Task::with(['taskType', 'staff:id,name', 'room:id,name_room'])
            ->where('status', 'pending_review')
            ->where('created_by', Auth::id())
            ->latest()->get();
        return response()->json($tasksToReview);
    }

    public function submitReview(Request $request, Task $task)
    {
        $validated = $request->validate([
            'decision' => 'required|in:completed,rejected',
            'rejection_notes' => 'nullable|string|max:1000'
        ]);

        if ($task->created_by !== Auth::id()) {
            abort(403, 'Anda tidak berhak mereview tugas ini.');
        }

        if ($task->staff) {
            $updateData = ['status' => $validated['decision']];
            if ($validated['decision'] === 'rejected') {
                $updateData['rejection_notes'] = $validated['rejection_notes'];
            } else {
                $updateData['rejection_notes'] = null;
            }
            $task->update($updateData);
            $task->staff->notify(new TaskReviewed($task));
            return response()->json(['message' => 'Tugas telah direview.', 'task' => $task]);
        }

        return response()->json(['message' => 'Gagal mengirim notifikasi: tidak ada staff yang mengerjakan.'], 422);
    }

    private function authorizeTaskAccess(Task $task)
    {
        $user = Auth::user();
        if (!($user->id === $task->created_by || $user->id === $task->user_id || in_array($user->role_id, ['SA00', 'MG00']))) {
            abort(403, 'AKSES DITOLAK');
        }
    }

    /**
     * Endpoint API untuk mengambil data riwayat tugas dengan filter.
     * (INI YANG DIPERBARUI)
     */
    public function getTaskHistory(Request $request)
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        $query = Task::with(['taskType', 'staff:id,name', 'creator:id,name'])
            ->whereNotNull('user_id'); // Hanya tampilkan tugas yang sudah dikerjakan

        // --- FILTERING BERDASARKAN PERAN ---

        // 1. Filter Departemen (Hanya untuk Manager & Admin)
        if (in_array($roleId, ['SA00', 'MG00'])) {
            $query->when($request->filled('department'), function ($q) use ($request) {
                return $q->whereHas('taskType', fn($subq) => $subq->where('departemen', $request->department));
            });
        }

        // 2. Filter Staff (Untuk Leader, Manager, & Admin)
        if (in_array($roleId, ['SA00', 'MG00']) || str_ends_with($roleId, '01')) {
            $query->when($request->filled('staff_id'), fn($q) => $q->where('user_id', $request->staff_id));
        }

        // 3. Filter Umum (Berlaku untuk semua peran)
        $query->when($request->filled('start_date'), fn($q) => $q->whereDate('updated_at', '>=', $request->start_date));
        $query->when($request->filled('end_date'), fn($q) => $q->whereDate('updated_at', '<=', $request->end_date));
        $query->when($request->filled('status'), fn($q) => $q->where('status', $request->status));

        // 4. Filter Pencarian (Search)
        $query->when($request->filled('search'), function ($q) use ($request) {
            $searchTerm = '%' . $request->search . '%';
            $q->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('title', 'like', $searchTerm)
                    ->orWhereHas('staff', fn($staffQuery) => $staffQuery->where('name', 'like', $searchTerm));
            });
        });

        // Jika user adalah Staff, hanya tampilkan tugas miliknya sendiri
        if (str_ends_with($roleId, '02')) {
            $query->where('user_id', $user->id);
        }

        $tasks = $query->latest('updated_at')->get();
        return response()->json($tasks);
    }

    public function getCompletedHistory()
    {
        $user = Auth::user();
        $roleId = $user->role_id;
        $query = Task::with(['taskType', 'staff:id,name', 'creator:id,name'])
            ->where('status', 'completed');
        if (str_ends_with($roleId, '02')) {
            $query->where('user_id', $user->id);
        } else if (str_ends_with($roleId, '01')) {
            $query->where('created_by', $user->id);
        }
        $completedTasks = $query->latest('updated_at')->get();
        return response()->json($completedTasks);
    }

    public function getInProgressTasks(Request $request)
    {
        $user = Auth::user();
        $roleId = $user->role_id;
        $query = Task::with(['taskType', 'staff:id,name', 'creator:id,name', 'room.floor.building'])
            ->whereIn('status', ['in_progress', 'pending_review']);
        if (str_ends_with($roleId, '01')) {
            $query->where('created_by', $user->id);
        }
        $query->when($request->filled('status'), fn($q) => $q->where('status', $request->status));
        $query->when($request->filled('search'), function ($q) use ($request) {
            $searchTerm = '%' . $request->search . '%';
            $q->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('title', 'like', $searchTerm)
                    ->orWhereHas('staff', fn($staffQuery) => $staffQuery->where('name', 'like', $searchTerm));
            });
        });
        $inProgressTasks = $query->latest('updated_at')->get();
        return response()->json($inProgressTasks);
    }

    /**
     * Mengambil semua tugas yang sedang aktif (belum selesai).
     * (NAMA METHOD DAN LOGIKA DIPERBARUI)
     */
    public function getActiveTasks(Request $request)
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        $query = Task::with(['taskType', 'staff:id,name', 'creator:id,name', 'room.floor.building'])
            // UBAH DI SINI: Sertakan status 'unassigned'
            ->whereIn('status', ['unassigned', 'in_progress', 'pending_review']);

        // Filter ini tetap berlaku untuk Leader agar hanya melihat tugas yang dibuatnya
        if (str_ends_with($roleId, '01')) {
            $query->where('created_by', $user->id);
        }

        // Filter berdasarkan status dari dropdown
        $query->when($request->filled('status'), fn($q) => $q->where('status', $request->status));

        // Filter pencarian
        $query->when($request->filled('search'), function ($q) use ($request) {
            $searchTerm = '%' . $request->search . '%';
            $q->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('title', 'like', $searchTerm)
                    ->orWhereHas('staff', fn($staffQuery) => $staffQuery->where('name', 'like', $searchTerm));
            });
        });

        $activeTasks = $query->latest('updated_at')->get();
        return response()->json($activeTasks);
    }

    public function getMyTaskHistory(Request $request)
    {
        $user = Auth::user();
        $query = Task::with(['taskType', 'room.floor.building'])
            ->where('user_id', $user->id);
        $query->when($request->input('status') === 'completed', fn($q) => $q->where('status', 'completed'), fn($q) => $q->whereIn('status', ['in_progress', 'rejected', 'pending_review']));
        $query->when($request->filled('search'), function ($q) use ($request) {
            $searchTerm = '%' . $request->search . '%';
            $q->where('title', 'like', $searchTerm);
        });
        $tasks = $query->latest('updated_at')->paginate(10);
        return response()->json($tasks);
    }
}
