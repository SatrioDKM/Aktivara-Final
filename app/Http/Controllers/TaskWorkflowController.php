<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use App\Notifications\NewTaskAvailable;
use App\Notifications\ReportSubmitted;
use App\Notifications\TaskClaimed;
use App\Notifications\TaskReviewed;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class TaskWorkflowController extends Controller
{
    // ===================================================================
    // METODE UNTUK MENAMPILKAN HALAMAN (VIEW) - Dipanggil dari web.php
    // ===================================================================

    public function createPage(): View
    {
        $data = [
            'taskTypes' => TaskType::orderBy('name_task')->get(),
            'buildings' => Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']),
            'floors' => Floor::with('building:id,name_building')->where('status', 'active')->get(['id', 'name_floor', 'building_id']),
            'rooms' => Room::with('floor:id,name_floor,building_id')->where('status', 'active')->get(['id', 'name_room', 'floor_id']),
            'assets' => Asset::where('status', 'available')->orderBy('name_asset')->get(),
            'staffUsers' => User::whereIn('role_id', ['HK02', 'TK02', 'SC02', 'PK02', 'WH02'])->orderBy('name')->get(['id', 'name']),
        ];
        return view('backend.tasks.create', compact('data'));
    }

    public function showPage(string $id): View
    {
        $task = Task::with(['taskType', 'room.floor.building', 'asset', 'creator', 'staff'])->findOrFail($id);
        $this->authorizeTaskAccess($task);
        $data = [
            'task' => $task,
            'assets' => Asset::where('asset_type', 'fixed_asset')->orderBy('name_asset')->get(['id', 'name_asset', 'serial_number']),
        ];
        return view('backend.tasks.show', compact('data'));
    }

    public function monitoringPage(): View
    {
        $data = []; // Data diambil via API
        return view('backend.tasks.monitoring', compact('data'));
    }

    public function availablePage(): View
    {
        $data = []; // Data diambil via API
        return view('backend.tasks.available', compact('data'));
    }

    /**
     * Menampilkan halaman gabungan untuk Riwayat Tugas Staff.
     * INI ADALAH METHOD YANG DIPERBAIKI SESUAI PERMINTAAN ANDA.
     */
    public function showMyHistoryPage(): View
    {
        $data = []; // Data untuk tabel diambil oleh API, jadi ini dikosongkan.
        return view('backend.tasks.my_history', compact('data'));
    }

    public function reviewPage(): View
    {
        $data = []; // Data diambil via API
        return view('backend.tasks.review_list', compact('data'));
    }

    public function historyPage(): View
    {
        $user = Auth::user();
        $roleId = $user->role_id;
        $staffQuery = User::whereIn('role_id', ['HK02', 'TK02', 'SC02', 'PK02', 'WH02']);

        if (str_ends_with($roleId, '01')) {
            $departmentCode = substr($roleId, 0, 2);
            $staffQuery->where('role_id', $departmentCode . '02');
        }

        $data = [
            'staffUsers' => $staffQuery->orderBy('name')->get(['id', 'name']),
            'departments' => in_array($roleId, ['SA00', 'MG00']) ? TaskType::whereNotNull('departemen')->distinct()->pluck('departemen') : [],
        ];
        return view('backend.tasks.history', compact('data'));
    }

    // ===================================================================
    // METODE UNTUK ENDPOINT API (JSON) - Dipanggil dari api.php
    // ===================================================================

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required|string|max:255',
            'task_type_id' => 'required|exists:task_types,id',
            'priority' => 'required|in:low,medium,high,critical',
            'description' => 'nullable|string',
            'room_id' => 'nullable|exists:rooms,id',
            'asset_id' => 'nullable|exists:assets,id',
            'user_id' => 'nullable|exists:users,id', // Untuk penugasan langsung
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task = Task::create(array_merge($validator->validated(), [
            'created_by' => Auth::id(),
            'status' => request('user_id') ? 'in_progress' : 'unassigned',
        ]));

        $task->load('taskType');

        $recipients = null;
        if (request('user_id')) { // Notif ke staff yang ditugaskan
            $recipients = User::find(request('user_id'));
        } elseif ($task->taskType->departemen && $task->taskType->departemen !== 'UMUM') { // Notif ke departemen
            $staffRole = $task->taskType->departemen . '02';
            $recipients = User::where('role_id', $staffRole)->get();
        }
        if ($recipients && $recipients->isNotEmpty()) {
            Notification::send($recipients, new NewTaskAvailable($task));
        }

        return response()->json(['message' => 'Tugas berhasil dibuat!'], 201);
    }

    public function claimTask(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $task = Task::where('id', $id)->lockForUpdate()->firstOrFail();
                if ($task->status !== 'unassigned') {
                    throw new \Exception('Tugas ini sudah diambil oleh staff lain.');
                }
                $task->update(['user_id' => Auth::id(), 'status' => 'in_progress']);
                if ($task->creator) {
                    Notification::send($task->creator, new TaskClaimed($task, Auth::user()));
                }
            });
            return response()->json(['message' => 'Tugas berhasil diambil!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengambil tugas. Mungkin sudah diambil staff lain.'], 409);
        }
    }

    public function submitReport(string $id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $validator = Validator::make(request()->all(), [
            'report_text' => 'required|string|min:10',
            'asset_id' => 'nullable|exists:assets,id',
            'image_before' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'image_after' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($task) {
                $data = ['report_text' => request('report_text'), 'asset_id' => request('asset_id')];
                if (request()->hasFile('image_before')) {
                    if ($task->image_before) Storage::disk('public')->delete($task->image_before);
                    $data['image_before'] = request()->file('image_before')->store('reports', 'public');
                }
                if (request()->hasFile('image_after')) {
                    if ($task->image_after) Storage::disk('public')->delete($task->image_after);
                    $data['image_after'] = request()->file('image_after')->store('reports', 'public');
                }
                $data['status'] = 'pending_review';
                $task->update($data);

                if ($task->creator) {
                    Notification::send($task->creator, new ReportSubmitted($task, Auth::user()));
                }
            });
            return response()->json(['message' => 'Laporan berhasil dikirim dan menunggu review.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengirim laporan: ' . $e->getMessage()], 500);
        }
    }

    public function submitReview(string $id)
    {
        $task = Task::findOrFail($id);
        if (Auth::id() !== $task->created_by) {
            abort(403, 'Anda tidak berwenang mereview tugas ini.');
        }

        $validator = Validator::make(request()->all(), [
            'decision' => 'required|in:completed,rejected',
            'rejection_notes' => 'required_if:decision,rejected|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task->update([
            'status' => request('decision'),
            'reviewed_by' => Auth::id(),
            'rejection_notes' => request('decision') === 'rejected' ? request('rejection_notes') : null,
        ]);

        if ($task->staff) {
            try {
                Notification::send($task->staff, new TaskReviewed($task));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi review tugas: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Review berhasil dikirim.']);
    }

    // --- API Methods for fetching data ---

    public function showAvailable()
    {
        $userDepartment = substr(Auth::user()->role_id, 0, 2);
        $tasks = Task::with(['room.floor.building', 'creator:id,name', 'taskType'])
            ->where('status', 'unassigned')
            ->whereHas('taskType', function ($query) use ($userDepartment) {
                $query->where('departemen', $userDepartment)->orWhere('departemen', 'UMUM');
            })
            ->latest()
            ->paginate(request('perPage', 10));
        return response()->json($tasks);
    }

    public function getActiveTasks()
    {
        $user = Auth::user();
        $query = Task::with(['taskType', 'staff:id,name', 'creator:id,name', 'room.floor.building'])
            ->whereIn('status', ['unassigned', 'in_progress', 'pending_review', 'rejected']);

        if (str_ends_with($user->role_id, '01')) { // Leader
            $query->where('created_by', $user->id);
        }

        $query->when(request('status'), fn($q, $status) => $q->where('status', $status));
        $query->when(request('search'), function ($q, $search) {
            $q->where(fn($sub) => $sub->where('title', 'like', "%{$search}%")->orWhereHas('staff', fn($s) => $s->where('name', 'like', "%{$search}%")));
        });

        $tasks = $query->latest('updated_at')->paginate(request('perPage', 10));
        return response()->json($tasks);
    }

    public function getMyTaskHistory()
    {
        $query = Task::with(['creator:id,name', 'taskType'])->where('user_id', Auth::id());
        $query->when(request('status'), function ($q, $status) {
            return $status === 'active' ? $q->whereIn('status', ['in_progress', 'rejected']) : $q->where('status', $status);
        });
        $query->when(request('start_date'), fn($q, $date) => $q->whereDate('updated_at', '>=', $date));
        $query->when(request('end_date'), fn($q, $date) => $q->whereDate('updated_at', '<=', $date));
        $query->when(request('search'), fn($q, $search) => $q->where('title', 'like', "%{$search}%"));

        $history = $query->latest('updated_at')->paginate(request('perPage', 10));
        return response()->json($history);
    }

    public function getReviewList()
    {
        $tasks = Task::with(['taskType', 'staff:id,name', 'room:id,name_room'])
            ->where('status', 'pending_review')
            ->where('created_by', Auth::id())
            ->latest()
            ->paginate(request('perPage', 10));
        return response()->json($tasks);
    }

    public function getTaskHistory()
    {
        $user = Auth::user();
        $query = Task::with(['taskType', 'staff:id,name', 'creator:id,name'])->whereNotNull('user_id');

        if (in_array($user->role_id, ['SA00', 'MG00'])) {
            $query->when(request('department'), fn($q, $dept) => $q->whereHas('taskType', fn($sub) => $sub->where('departemen', $dept)));
        }
        if (in_array($user->role_id, ['SA00', 'MG00']) || str_ends_with($user->role_id, '01')) {
            $query->when(request('staff_id'), fn($q, $staff) => $q->where('user_id', $staff));
        } else {
            $query->where('user_id', $user->id);
        }

        $query->when(request('start_date'), fn($q, $date) => $q->whereDate('updated_at', '>=', $date));
        $query->when(request('end_date'), fn($q, $date) => $q->whereDate('updated_at', '<=', $date));
        $query->when(request('status'), fn($q, $status) => $q->where('status', $status));
        $query->when(request('search'), function ($q, $search) {
            $q->where(fn($sub) => $sub->where('title', 'like', "%{$search}%")->orWhereHas('staff', fn($s) => $s->where('name', 'like', "%{$search}%")));
        });

        $tasks = $query->latest('updated_at')->paginate(request('perPage', 10));
        return response()->json($tasks);
    }

    private function authorizeTaskAccess(Task $task)
    {
        $user = Auth::user();
        if (in_array($user->role_id, ['SA00', 'MG00'])) return;
        if ($user->id === $task->created_by) return;
        if ($user->id === $task->user_id) return;
        abort(403, 'AKSES DITOLAK');
    }
}
