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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ReportSubmitted;
use App\Notifications\NewTaskAvailable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
        // Beri akses ke semua role yang berwenang (termasuk staff) untuk membuat tugas
        if (!in_array($user->role_id, ['SA00', 'MG00', 'HK01', 'TK01', 'SC01', 'PK01', 'HK02', 'TK02', 'SC02', 'PK02'])) {
            abort(403, 'AKSES DITOLAK');
        }

        $taskTypes = TaskType::orderBy('name_task')->get();
        $buildings = Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']);
        $floors = Floor::with('building:id,name_building')->where('status', 'active')->get(['id', 'name_floor', 'building_id']);
        $rooms = Room::with('floor:id,name_floor,building_id')->where('status', 'active')->get(['id', 'name_room', 'floor_id']);
        $assets = Asset::where('status', 'available')->orderBy('name_asset')->get();

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

    /**
     * Menampilkan detail tugas dan data pendukung untuk form laporan.
     */
    public function showPage(Task $task)
    {
        $this->authorizeTaskAccess($task);
        $task->load(['taskType', 'room.floor.building', 'asset', 'creator', 'staff']);
        $assets = Asset::where('asset_type', 'fixed_asset')->orderBy('name_asset')->get(['id', 'name_asset', 'serial_number']);
        return view('tasks.show', compact('task', 'assets'));
    }

    /**
     * Menampilkan halaman gabungan untuk Riwayat Tugas Staff.
     */
    public function showMyHistoryPage()
    {
        return view('tasks.my_history');
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
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'task_type_id' => 'required|exists:task_types,id',
            'priority' => 'required|in:low,medium,high,critical', // Priority sekarang wajib
            'description' => 'nullable|string',
            'room_id' => 'nullable|exists:rooms,id',
            'asset_id' => 'nullable|exists:assets,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task = Task::create(array_merge($validator->validated(), [
            'created_by' => Auth::id(),
            'status' => 'unassigned',
        ]));

        // Muat relasi yang dibutuhkan, termasuk taskType
        $task->load('taskType');

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
            'message' => 'Tugas berhasil dibuat!',
            'redirect_url' => route('dashboard')
        ], 201);
    }

    /**
     * API: Staff mengklaim tugas yang tersedia.
     * (LOGIKA NOTIFIKASI DIPERBARUI)
     */
    public function claimTask(Task $task)
    {
        try {
            DB::transaction(function () use ($task) {
                $taskToClaim = Task::where('id', $task->id)->lockForUpdate()->first();

                if ($taskToClaim->status !== 'unassigned') {
                    throw new \Exception('Tugas ini sudah diambil oleh staff lain.');
                }

                $taskToClaim->update([
                    'user_id' => Auth::id(),
                    'status' => 'in_progress',
                ]);

                // --- BLOK PENGIRIMAN NOTIFIKASI YANG LEBIH AMAN ---
                try {
                    if ($taskToClaim->creator) {
                        Notification::send($taskToClaim->creator, new TaskClaimed($taskToClaim, Auth::user()));
                    }
                } catch (\Exception $e) {
                    // Jika notifikasi gagal, catat error tapi jangan hentikan proses
                    Log::error('Gagal mengirim notifikasi klaim tugas: ' . $e->getMessage());
                }
                // -------------------------------------------------
            });

            return response()->json(['message' => 'Tugas berhasil diambil!']);
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

    /**
     * API: Staff mengirimkan laporan pengerjaan tugas.
     */
    public function submitReport(Request $request, string $id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $validator = Validator::make($request->all(), [
            'report_text' => 'required|string|min:10',
            'asset_id' => 'nullable|exists:assets,id',
            'image_before' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'image_after' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request, $task) {
                $dataToUpdate = $request->only(['report_text', 'asset_id']);

                if ($request->hasFile('image_before')) {
                    if ($task->image_before) Storage::disk('public')->delete($task->image_before);
                    $dataToUpdate['image_before'] = $request->file('image_before')->store('reports', 'public');
                }

                if ($request->hasFile('image_after')) {
                    if ($task->image_after) Storage::disk('public')->delete($task->image_after);
                    $dataToUpdate['image_after'] = $request->file('image_after')->store('reports', 'public');
                }

                $dataToUpdate['status'] = 'pending_review';
                $task->update($dataToUpdate);

                if ($task->creator) {
                    Notification::send($task->creator, new ReportSubmitted($task, Auth::user()));
                }
            });

            return response()->json(['message' => 'Laporan berhasil dikirim dan menunggu review.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengirim laporan: ' . $e->getMessage()], 500);
        }
    }

    public function showReviewList()
    {
        $tasksToReview = Task::with(['taskType', 'staff:id,name', 'room:id,name_room'])
            ->where('status', 'pending_review')
            ->where('created_by', Auth::id())
            ->latest()->get();
        return response()->json($tasksToReview);
    }

    /**
     * API: Leader mereview laporan dari Staff.
     * (LOGIKA DIPERBARUI)
     */
    public function submitReview(Request $request, Task $task)
    {
        // Pastikan hanya pembuat tugas yang bisa mereview
        if (Auth::id() !== $task->created_by) {
            abort(403, 'Anda tidak berwenang mereview tugas ini.');
        }

        $validator = Validator::make($request->all(), [
            'decision' => 'required|in:completed,rejected',
            'rejection_notes' => 'required_if:decision,rejected|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $decision = $request->decision;
        $notes = $request->rejection_notes;

        $task->update([
            'status' => $decision,
            'reviewed_by' => Auth::id(),
            'rejection_notes' => $decision === 'rejected' ? $notes : null,
        ]);

        try {
            if ($task->staff) {
                Notification::send($task->staff, new TaskReviewed($task));
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi review tugas: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Review berhasil dikirim.']);
    }

    private function authorizeTaskAccess(Task $task)
    {
        $user = Auth::user();
        if ($user->role_id === 'SA00' || $user->role_id === 'MG00') return; // Admin/Manager bisa lihat semua
        if ($user->id === $task->created_by) return; // Pembuat tugas bisa lihat
        if ($user->id === $task->user_id) return; // Pengerja tugas bisa lihat
        abort(403, 'AKSES DITOLAK');
    }

    /**
     * API: Mengambil data riwayat tugas pribadi Staff dengan filter yang berfungsi.
     * (FUNGSI LAMA DIGANTI DENGAN YANG INI)
     */
    public function getMyHistory(Request $request)
    {
        $user = Auth::user();

        $query = Task::with(['creator:id,name', 'taskType'])
            ->where('user_id', $user->id); // Hanya tugas milik staff yang login

        // Filter berdasarkan status
        $query->when($request->filled('status'), function ($q) use ($request) {
            if ($request->status === 'active') {
                return $q->whereIn('status', ['in_progress', 'rejected']);
            }
            return $q->where('status', $request->status);
        });

        // Filter berdasarkan tanggal
        $query->when($request->filled('start_date'), fn($q) => $q->whereDate('updated_at', '>=', $request->start_date));
        $query->when($request->filled('end_date'), fn($q) => $q->whereDate('updated_at', '<=', $request->end_date));

        // Filter pencarian
        $query->when($request->filled('search'), function ($q) use ($request) {
            $searchTerm = '%' . $request->search . '%';
            $q->where('title', 'like', $searchTerm);
        });

        $history = $query->latest('updated_at')->paginate(10);

        return response()->json($history);
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

    /**
     * API: Mengambil data riwayat tugas pribadi Staff dengan filter yang berfungsi.
     * (LOGIKA FILTER DIPERBAIKI)
     */
    public function getMyTaskHistory(Request $request)
    {
        $user = Auth::user();
        $query = Task::with(['creator:id,name', 'taskType'])
            ->where('user_id', $user->id);

        // Filter berdasarkan status
        $query->when($request->filled('status'), function ($q) use ($request) {
            if ($request->status === 'active') {
                return $q->whereIn('status', ['in_progress', 'rejected']);
            }
            return $q->where('status', $request->status);
        });

        // Filter berdasarkan tanggal
        $query->when($request->filled('start_date'), fn($q) => $q->whereDate('updated_at', '>=', $request->start_date));
        $query->when($request->filled('end_date'), fn($q) => $q->whereDate('updated_at', '<=', $request->end_date));

        // Filter pencarian
        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%');
        });

        return response()->json($query->latest('updated_at')->paginate(10));
    }
}
