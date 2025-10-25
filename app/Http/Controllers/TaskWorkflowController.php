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
     * Menampilkan halaman form untuk membuat tugas baru.
     */
    public function createPage(): View
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        // --- PERBAIKAN FILTER JENIS TUGAS DI SINI ---
        $taskTypeQuery = TaskType::query();

        // Jika bukan Manager atau Superadmin, filter berdasarkan departemen user
        if (!in_array($roleId, ['SA00', 'MG00'])) {
            $userDepartment = substr($roleId, 0, 2); // Ambil kode departemen user
            $taskTypeQuery->where(function ($query) use ($userDepartment) {
                $query->where('departemen', $userDepartment)
                    ->orWhere('departemen', 'UMUM'); // Selalu sertakan UMUM
            });
        }
        // Manager & Superadmin bisa melihat semua (tidak perlu filter where)

        $taskTypes = $taskTypeQuery->orderBy('name_task')->get();
        // --- AKHIR PERBAIKAN FILTER ---

        $data = [
            'taskTypes' => $taskTypes, // Gunakan hasil query yang sudah difilter
            'buildings' => Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']),
            'assets' => Asset::orderBy('name_asset')->get(['id', 'name_asset', 'serial_number']),
        ];
        return view('backend.tasks.create', compact('data'));
    }


    /**
     * Menampilkan halaman detail tugas spesifik.
     */
    public function showPage($taskId): View
    {
        $task = Task::with([
            'taskType',
            'room.floor.building',
            'asset',
            'creator',
            'assignee'
        ])->findOrFail($taskId);

        $this->authorizeTaskAccess($task);

        $data = [
            'task' => $task,
            'assets' => Asset::where('asset_type', 'fixed_asset')
                ->orderBy('name_asset')
                ->get(['id', 'name_asset', 'serial_number']),
        ];
        return view('backend.tasks.show', compact('data'));
    }

    /**
     * Menampilkan halaman papan tugas (job board) untuk staff.
     */
    public function availablePage(): View
    {
        // --- PERBAIKAN: Tambah $data = [] ---
        $data = [];
        return view('backend.tasks.available', compact('data'));
    }

    /**
     * Menampilkan halaman tugas aktif milik staff.
     */
    public function myTasksPage(): View
    {
        // --- PERBAIKAN: Tambah $data = [] ---
        $data = [];
        return view('backend.tasks.my_tasks', compact('data'));
    }

    /**
     * Menampilkan halaman untuk mereview laporan tugas dari staff.
     */
    public function reviewPage(): View
    {
        // --- PERBAIKAN: Tambah $data = [] ---
        $data = [];
        return view('backend.tasks.review_list', compact('data'));
    }

    /**
     * Menampilkan halaman monitoring tugas aktif untuk atasan.
     */
    public function monitoringPage(): View
    {
        // --- PERBAIKAN: Tambah $data = [] ---
        $data = [];
        return view('backend.tasks.monitoring', compact('data'));
    }

    /**
     * Menampilkan halaman riwayat tugas pribadi untuk staff.
     */
    public function showMyHistoryPage(): View
    {
        // --- PERBAIKAN: Tambah $data = [] ---
        $data = [];
        return view('backend.tasks.my_history', compact('data'));
    }

    /**
     * Menampilkan halaman riwayat tugas yang telah selesai.
     */
    public function completedHistoryPage(): View
    {
        // --- PERBAIKAN: Tambah $data = [] ---
        $data = [];
        // Path view sudah benar
        return view('backend.tasks.completed_history', compact('data'));
    }

    /**
     * Menampilkan halaman riwayat & laporan tugas untuk atasan (Leader, Manager, Admin).
     * Metode ini mengirim data filter yang diperlukan oleh view.
     * --- PERBAIKAN TOTAL DI SINI ---
     */
    public function historyPage(): View
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        // Ambil daftar Staff
        $staffQuery = User::whereIn('role_id', ['HK02', 'TK02', 'SC02', 'PK02', 'WH02']);
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
                ->orderBy('departemen')
                ->pluck('departemen');
        }

        // Gabungkan semua data ke dalam satu variabel $data
        $data = [
            'staffUsers' => $staffUsers,
            'departments' => $departments
        ];

        // Arahkan ke path view yang benar
        return view('backend.tasks.history', compact('data'));
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
     * Logika di-update untuk memberikan pesan error yang lebih deskriptif.
     */
    public function claimTask(Request $request, string $id): JsonResponse
    {
        try {
            $claimedTask = null;
            DB::transaction(function () use ($id, &$claimedTask) {
                $taskToClaim = Task::where('id', $id)->lockForUpdate()->firstOrFail();

                if ($taskToClaim->status !== 'unassigned' || $taskToClaim->user_id !== null) {
                    throw new \Exception('Tugas ini sudah diambil oleh staff lain.');
                }

                $taskToClaim->update([
                    'user_id' => Auth::id(),
                    'status' => 'in_progress',
                ]);

                $claimedTask = $taskToClaim;
            });

            // Kirim notifikasi setelah transaksi berhasil
            if ($claimedTask && $claimedTask->creator) {
                try {
                    Notification::send($claimedTask->creator, new TaskClaimed($claimedTask, Auth::user()));
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim notifikasi klaim tugas: ' . $e->getMessage());
                }
            }

            return response()->json(['message' => 'Tugas berhasil diambil!']);
        } catch (\Exception $e) {
            // --- PERBAIKAN PESAN ERROR DI SINI ---
            // Ambil data terbaru dari tugas untuk memberikan info debug yang lebih baik.
            $freshTask = Task::with('assignee:id,name')->find($id);
            $message = 'Gagal mengambil tugas.';

            if ($freshTask) {
                $message .= " Status saat ini: '{$freshTask->status}'.";
                if ($freshTask->assignee) {
                    $message .= " Telah diambil oleh: {$freshTask->assignee->name}.";
                }
            } else {
                $message .= " Tugas tidak ditemukan.";
            }

            return response()->json(['message' => $message], 409); // 409 Conflict
        }
    }

    /**
     * API: Menampilkan daftar tugas yang tersedia untuk staff.
     * Query diperketat untuk memastikan user_id juga NULL.
     */
    public function showAvailable(): JsonResponse
    {
        $userDepartment = substr(Auth::user()->role_id, 0, 2);

        $availableTasks = Task::with(['room.floor.building', 'creator:id,name', 'taskType'])
            // --- PERBAIKAN QUERY DI SINI ---
            ->where('status', 'unassigned')
            ->whereNull('user_id') // Pastikan tugas belum dimiliki siapa pun
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
     * * --- PERBAIKAN TOTAL DI SINI ---
     * Mengganti Route-Model Binding (Task $task) dengan manual fetching ($id)
     * untuk memastikan semua atribut model termuat sebelum otorisasi.
     */
    public function show(string $id): JsonResponse
    {
        // 1. Ambil data Task secara manual menggunakan findOrFail.
        // Ini memastikan semua kolom dari database (termasuk created_by dan user_id) termuat.
        $task = Task::findOrFail($id);

        // 2. Lakukan otorisasi. Sekarang $task->created_by dan $task->user_id memiliki nilai yang benar.
        $this->authorizeTaskAccess($task);

        // 3. Load relasi yang dibutuhkan untuk ditampilkan di frontend.
        $task->load(['taskType', 'room.floor.building', 'asset', 'creator', 'assignee']);

        // 4. Kembalikan data sebagai JSON.
        return response()->json($task);
    }

    /**
     * API: Staff mengirimkan laporan pengerjaan tugas.
     * Metode inilah yang seharusnya dipanggil oleh form Anda.
     */
    public function submitReport(Request $request, string $id): JsonResponse
    {
        // Langkah 1: Temukan tugas berdasarkan ID-nya terlebih dahulu.
        $task = Task::findOrFail($id);

        // Langkah 2: Lakukan verifikasi kepemilikan secara eksplisit.
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak berhak melaporkan tugas ini.'], 403);
        }

        // Langkah 3: Lakukan validasi input sesuai dengan form
        $validator = Validator::make($request->all(), [
            'report_text' => 'required|string|min:10',
            'image_before' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'image_after' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Langkah 4: Proses update data dan upload file.
        try {
            DB::transaction(function () use ($request, $task) {
                $dataToUpdate['report_text'] = $request->input('report_text');

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
