<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Task;
use App\Models\User;
use App\Models\Asset;
use App\Models\TaskType;
use Illuminate\Http\Request;
use App\Notifications\TaskClaimed;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewTaskAvailable;
use Illuminate\Support\Facades\Notification;

class TaskWorkflowController extends Controller
{
    // ===================================================================
    // METODE UNTUK MENAMPILKAN HALAMAN (VIEW) - Dipanggil dari web.php
    // ===================================================================

    public function createPage()
    {
        $taskTypes = TaskType::orderBy('name_task')->get();
        $rooms = Room::with('floor.building')->where('status', 'active')->get();
        $assets = Asset::where('status', 'available')->orderBy('name_asset')->get();
        return view('tasks.create', compact('taskTypes', 'rooms', 'assets'));
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

    // ===================================================================
    // METODE UNTUK ENDPOINT API (JSON) - Dipanggil dari api.php
    // ===================================================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'task_type_id' => 'required|exists:task_types,id',
            'room_id' => 'nullable|exists:rooms,id',
            'asset_id' => 'nullable|exists:assets,id',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $task = Task::create($validated + ['created_by' => Auth::id(), 'status' => 'unassigned']);
        $task->load('creator', 'taskType'); // Muat relasi yang dibutuhkan

        // --- KIRIM NOTIFIKASI TUGAS BARU ---
        $taskType = $task->taskType;
        if ($taskType && $taskType->departemen) {
            // Cari semua staff di departemen yang sesuai (misal: 'TK02')
            $staffUsers = User::where('role_id', $taskType->departemen . '02')->get();
            if ($staffUsers->isNotEmpty()) {
                Notification::send($staffUsers, new NewTaskAvailable($task));
            }
        }
        // ------------------------------------

        return response()->json($task->load('taskType'), 201);
    }

    /**
     * Proses klaim tugas oleh Staff.
     */
    public function claimTask(Task $task)
    {
        try {
            $claimedTask = DB::transaction(function () use ($task) {
                $taskToClaim = Task::where('id', $task->id)->where('status', 'unassigned')->lockForUpdate()->firstOrFail();

                $taskToClaim->update([
                    'user_id' => Auth::id(),
                    'status' => 'in_progress',
                ]);

                // --- KIRIM NOTIFIKASI TUGAS DIAMBIL ---
                // Kirim notifikasi ke pembuat tugas (Leader)
                $task->creator->notify(new TaskClaimed($task, Auth::user()));
                // ---------------------------------------

                return $taskToClaim;
            });
            return response()->json(['message' => 'Tugas berhasil diambil!', 'task' => $claimedTask]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengambil tugas. Mungkin sudah diambil staff lain.'], 409);
        }
    }

    public function showAvailable()
    {
        $userDepartment = substr(Auth::user()->role_id, 0, 2);
        $availableTasks = Task::with(['taskType', 'room.floor.building', 'creator:id,name'])
            ->where('status', 'unassigned')
            ->whereHas('taskType', fn($q) => $q->where('departemen', $userDepartment))
            ->latest()->get();
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
        $validated = $request->validate(['decision' => 'required|in:completed,rejected', 'review_notes' => 'nullable|string']);
        if ($task->created_by !== Auth::id()) {
            abort(403, 'Anda tidak berhak mereview tugas ini.');
        }
        $task->update(['status' => $validated['decision']]);
        return response()->json(['message' => 'Tugas telah direview.', 'task' => $task]);
    }

    private function authorizeTaskAccess(Task $task)
    {
        $user = Auth::user();
        if (!($user->id === $task->created_by || $user->id === $task->user_id || in_array($user->role_id, ['SA00', 'MG00']))) {
            abort(403, 'AKSES DITOLAK');
        }
    }

    /**
     * Menampilkan halaman Riwayat Tugas dengan data untuk filter.
     * (INI METODE BARU)
     */
    public function historyPage()
    {
        // Ambil data untuk mengisi dropdown filter
        $taskTypes = TaskType::orderBy('name_task')->get(['id', 'name_task']);
        $staffUsers = User::where('role_id', 'like', '%02')->orderBy('name')->get(['id', 'name']);

        return view('history.tasks', compact('taskTypes', 'staffUsers'));
    }

    /**
     * Endpoint API untuk mengambil data riwayat tugas dengan filter.
     * (INI METODE BARU)
     */
    public function getTaskHistory(Request $request)
    {
        $query = Task::with(['taskType', 'staff:id,name', 'creator:id,name'])
            ->whereNotNull('user_id'); // Hanya tampilkan tugas yang sudah pernah diambil

        // Terapkan filter secara dinamis jika ada
        $query->when($request->filled('start_date'), function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->start_date);
        });

        $query->when($request->filled('end_date'), function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->end_date);
        });

        $query->when($request->filled('task_type_id'), function ($q) use ($request) {
            $q->where('task_type_id', $request->task_type_id);
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->filled('staff_id'), function ($q) use ($request) {
            $q->where('user_id', $request->staff_id);
        });

        $tasks = $query->latest()->get();

        return response()->json($tasks);
    }
}
