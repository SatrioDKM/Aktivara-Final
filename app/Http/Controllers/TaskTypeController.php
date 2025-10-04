<?php

namespace App\Http\Controllers;

use App\Models\TaskType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class TaskTypeController extends Controller
{
    /**
     * Menampilkan halaman daftar jenis tugas (index).
     */
    public function viewPage(): View
    {
        return view('backend.master.task_types.index');
    }

    /**
     * Menampilkan halaman formulir tambah jenis tugas.
     */
    public function create(): View
    {
        return view('backend.master.task_types.create');
    }

    /**
     * Menampilkan halaman detail jenis tugas.
     */
    public function showPage(string $id): View
    {
        $data = ['taskType' => TaskType::findOrFail($id)];
        return view('backend.master.task_types.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir edit jenis tugas.
     */
    public function edit(string $id): View
    {
        $data = ['taskType' => TaskType::findOrFail($id)];
        return view('backend.master.task_types.edit', compact('data'));
    }

    // ===================================================================
    // API METHODS
    // ===================================================================

    /**
     * API: Menampilkan daftar semua jenis tugas dengan paginasi dan filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = TaskType::query();

        $query->when($request->input('search'), function ($q, $search) {
            $q->where('name_task', 'like', '%' . $search . '%');
        });

        $query->when($request->input('department'), function ($q, $department) {
            $q->where('departemen', $department);
        });

        $taskTypes = $query->latest()->paginate($request->input('perPage', 10));

        return response()->json($taskTypes);
    }

    /**
     * API: Menyimpan data jenis tugas baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name_task' => 'required|string|max:100|unique:task_types,name_task',
            'departemen' => 'required|string|max:50',
            'priority_level' => 'required|in:low,medium,high,critical',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $taskType = TaskType::create($request->all());

        return response()->json($taskType, 201);
    }

    /**
     * API: Menampilkan satu data jenis tugas spesifik.
     */
    public function show(string $id): JsonResponse
    {
        $taskType = TaskType::findOrFail($id);
        return response()->json($taskType);
    }

    /**
     * API: Memperbarui data jenis tugas yang sudah ada.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $taskType = TaskType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_task' => 'required|string|max:100|unique:task_types,name_task,' . $taskType->id,
            'departemen' => 'required|string|max:50',
            'priority_level' => 'required|in:low,medium,high,critical',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $taskType->update($request->all());

        return response()->json($taskType);
    }

    /**
     * API: Menghapus data jenis tugas.
     */
    public function destroy(string $id): JsonResponse
    {
        $taskType = TaskType::findOrFail($id);
        $taskType->delete();
        return response()->json(['message' => 'Jenis tugas berhasil dihapus.'], 200);
    }

    /**
     * API: Mengambil jenis tugas berdasarkan kode departemen.
     */
    public function getByDepartment(string $department_code): JsonResponse
    {
        $taskTypes = TaskType::where('departemen', $department_code)
            ->orWhere('departemen', 'UMUM')
            ->orderBy('name_task')
            ->get(['id', 'name_task as text']); // Kompatibel dengan Select2

        return response()->json($taskTypes);
    }
}
