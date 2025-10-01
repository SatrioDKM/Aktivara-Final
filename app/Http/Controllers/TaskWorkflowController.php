<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Task;
use App\Models\User;
use App\Models\Asset;
use App\Models\Floor;
use App\Models\Building;
use App\Models\TaskType;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
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
    // METODE UNTUK MENAMPILKAN HALAMAN (VIEW)
    // ===================================================================

    /**
     * Menampilkan halaman form untuk membuat tugas.
     * Query dioptimalkan dengan memilih kolom spesifik untuk data master.
     */
    public function createPage(): View
    {
        $data = [
            'taskTypes' => TaskType::orderBy('name_task')->get(),
            'buildings' => Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']),
            'assets' => Asset::orderBy('name_asset')->get(['id', 'name_asset', 'serial_number']),
            // Data lantai dan ruangan akan diambil via API untuk performa lebih baik
        ];
        return view('backend.tasks.create', compact('data'));
    }

    /**
     * Menampilkan halaman detail tugas.
     * PERBAIKAN TOTAL: Memastikan $task selalu ada dan relasi di-load dengan aman.
     */
    public function showPage($taskId): View
    {
        // Gunakan findOrFail untuk mendapatkan tugas atau menampilkan error 404 jika tidak ditemukan.
        $task = Task::with([
            'taskType',
            'room.floor.building',
            'asset',
            'creator',
            'assignee' // Pastikan nama relasi ini benar
        ])->findOrFail($taskId);

        // Panggil otorisasi setelah task ditemukan
        $this->authorizeTaskAccess($task);

        // Siapkan data untuk dikirim ke view
        $data = [
            'task' => $task,
            'assets' => Asset::where('asset_type', 'fixed_asset')
                ->orderBy('name_asset')
                ->get(['id', 'name_asset', 'serial_number']),
        ];

        return view('backend.tasks.show', compact('data'));
    }

    public function availablePage()
    {
        return view('tasks.available');
    }

    public function myTasksPage()
    {
        return view('tasks.my_tasks');
    }

    public function reviewPage()
    {
        return view('tasks.review_list');
    }

    public function monitoringPage()
    {
        return view('tasks.monitoring');
    }

    /**
     * Menampilkan halaman gabungan untuk Riwayat Tugas Staff.
     */
    public function myHistoryPage()
    {
        return view('tasks.my_history');
    }

    public function completedHistoryPage()
    {
        return view('tasks.completed_history');
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


    // ===================================================================
    // METODE UNTUK ENDPOINT API (JSON)
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
     * API: Menampilkan daftar tugas yang tersedia untuk departemen staff.
     */
    public function showAvailable(): JsonResponse
    {
        $userDepartment = substr(Auth::user()->role_id, 0, 2);

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

    /**
     * API: Menampilkan tugas aktif (in_progress, rejected) milik staff yang login.
     */
    public function myTasks(): JsonResponse
    {
        $myTasks = Task::with(['taskType', 'room.floor.building'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['in_progress', 'rejected'])
            ->latest()->get();

        return response()->json($myTasks);
    }

    /**
     * API: Mengambil detail tugas untuk ditampilkan.
     * Pastikan metode ini memuat semua relasi yang diperlukan.
     */
    public function show(Task $task): JsonResponse
    {
        $this->authorizeTaskAccess($task);

        // PERBAIKAN: Ganti 'staff' menjadi 'assignee' dan pastikan semua relasi ada
        $task->load(['taskType', 'room.floor.building', 'asset', 'creator', 'assignee']);

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

    /**
     * API: Menampilkan daftar tugas yang perlu direview oleh leader.
     */
    public function showReviewList(): JsonResponse
    {
        // PERBAIKAN: Mengganti 'staff:id,name' menjadi 'assignee:id,name'
        $tasksToReview = Task::with(['taskType', 'assignee:id,name', 'room:id,name_room'])
            ->where('status', 'pending_review')
            ->where('created_by', Auth::id())
            ->latest()->get();

        return response()->json($tasksToReview);
    }

    /**
     * API: Leader mereview laporan dari Staff.
     * (LOGIKA DIPERBARUI)
     */
    public function submitReview(Request $request, string $id): JsonResponse
    {
        // --- PERBAIKAN UTAMA DI SINI ---
        // 1. Ubah parameter dari `Task $task` menjadi `string $id`.
        // 2. Ambil data task secara manual menggunakan findOrFail untuk memastikan semua kolom termuat.
        $task = Task::findOrFail($id);

        $user = Auth::user();
        $isCreator = $user->id === $task->created_by;
        $isManagerOrAdmin = in_array($user->role_id, ['SA00', 'MG00']);

        // Logika otorisasi sekarang akan bekerja dengan benar karena $task->created_by sudah ada nilainya.
        if (!$isCreator && !$isManagerOrAdmin) {
            return response()->json(['message' => 'Anda tidak berwenang mereview tugas ini.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'decision' => 'required|in:completed,rejected',
            'rejection_notes' => 'required_if:decision,rejected|nullable|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $decision = $request->input('decision');
        $notes = $request->input('rejection_notes');

        $task->update([
            'status' => $decision,
            'reviewed_by' => $user->id,
            'rejection_notes' => $decision === 'rejected' ? $notes : null,
        ]);

        try {
            if ($task->assignee) {
                Notification::send($task->assignee, new TaskReviewed($task));
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi review tugas: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Review berhasil dikirim.']);
    }

    /**
     * API: Mengambil data riwayat tugas dengan filter lengkap dan paginasi.
     */
    public function getTaskHistory(Request $request): JsonResponse
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        // PERBAIKAN: Mengganti 'staff:id,name' menjadi 'assignee:id,name'
        $query = Task::with(['taskType', 'assignee:id,name', 'creator:id,name'])
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
                // PERBAIKAN: Mengganti 'staff' menjadi 'assignee' pada orWhereHas
                $subQuery->where('title', 'like', $searchTerm)
                    ->orWhereHas('assignee', fn($staffQuery) => $staffQuery->where('name', 'like', $searchTerm));
            });
        });

        // Jika user adalah Staff, hanya tampilkan tugas miliknya sendiri
        if (str_ends_with($roleId, '02')) {
            $query->where('user_id', $user->id);
        }

        // OPTIMASI: Menggunakan paginate() untuk menangani data riwayat yang besar
        $tasks = $query->latest('updated_at')->paginate(10);

        return response()->json($tasks);
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

    /**
     * API: Mengambil semua tugas yang aktif (unassigned, in_progress, pending_review).
     * Fungsi ini digunakan untuk halaman Monitoring Tugas.
     */
    public function getActiveTasks(Request $request): JsonResponse
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        // PERBAIKAN: Mengganti 'staff:id,name' menjadi 'assignee:id,name'
        $query = Task::with(['taskType', 'assignee:id,name', 'creator:id,name', 'room.floor.building'])
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
                // PERBAIKAN: Mengganti 'staff' menjadi 'assignee' pada orWhereHas
                $subQuery->where('title', 'like', $searchTerm)
                    ->orWhereHas('assignee', fn($staffQuery) => $staffQuery->where('name', 'like', $searchTerm));
            });
        });

        // Untuk halaman monitoring, ->get() cocok karena jumlah tugas aktif biasanya terkendali.
        $activeTasks = $query->latest('updated_at')->get();

        return response()->json($activeTasks);
    }

    /**
     * API: Mengambil data riwayat tugas yang statusnya 'completed' dengan paginasi.
     */
    public function getCompletedHistory(Request $request): JsonResponse
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        // PERBAIKAN: Mengganti 'staff:id,name' menjadi 'assignee:id,name'
        $query = Task::with(['taskType', 'assignee:id,name', 'creator:id,name'])
            ->where('status', 'completed');

        // Terapkan filter berdasarkan peran
        if (str_ends_with($roleId, '02')) { // Jika Staff
            $query->where('user_id', $user->id);
        } else if (str_ends_with($roleId, '01')) { // Jika Leader
            $query->where('created_by', $user->id);
        }
        // Admin & Manager bisa melihat semua

        // OPTIMASI: Menggunakan paginate()
        $completedTasks = $query->latest('updated_at')->paginate(10);

        return response()->json($completedTasks);
    }

    private function authorizeTaskAccess(Task $task)
    {
        $user = Auth::user();
        if ($user->role_id === 'SA00' || $user->role_id === 'MG00') return; // Admin/Manager bisa lihat semua
        if ($user->id === $task->created_by) return; // Pembuat tugas bisa lihat
        if ($user->id === $task->user_id) return; // Pengerja tugas bisa lihat
        abort(403, 'AKSES DITOLAK');
    }
}
