<?php

namespace App\Http\Controllers;

use App\Models\TaskType;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class TaskTypeController extends Controller
{
    /**
     * Menampilkan halaman utama (index).
     */
    public function viewPage(): View
    {
        return view('backend.master.task_types.index');
    }

    /**
     * Menampilkan halaman formulir tambah.
     */
    public function create(): View
    {
        return view('backend.master.task_types.create');
    }

    /**
     * Menampilkan halaman detail (show).
     */
    public function showPage(string $id): View
    {
        $data = ['taskType' => TaskType::findOrFail($id)];
        return view('backend.master.task_types.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir edit.
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
    public function index()
    {
        $query = TaskType::query();

        // Filter pencarian
        if (request('search', '')) {
            $query->where('name_task', 'like', '%' . request('search') . '%');
        }

        // Filter departemen
        if (request('department', '')) {
            $query->where('departemen', request('department'));
        }

        $taskTypes = $query->latest()->paginate(request('perPage', 10));

        return response()->json($taskTypes);
    }

    /**
     * API: Menyimpan data jenis tugas baru.
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name_task' => 'required|string|max:100|unique:task_types,name_task',
            'departemen' => 'required|string|max:50',
            'priority_level' => 'required|in:low,medium,high,critical',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $taskType = TaskType::create(request()->all());

        return response()->json($taskType, 201);
    }

    /**
     * API: Menampilkan satu data jenis tugas spesifik.
     */
    public function show(string $id)
    {
        $taskType = TaskType::findOrFail($id);
        return response()->json($taskType);
    }

    /**
     * API: Memperbarui data jenis tugas yang sudah ada.
     */
    public function update(string $id)
    {
        $taskType = TaskType::findOrFail($id);

        $validator = Validator::make(request()->all(), [
            'name_task' => 'required|string|max:100|unique:task_types,name_task,' . $taskType->id,
            'departemen' => 'required|string|max:50',
            'priority_level' => 'required|in:low,medium,high,critical',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $taskType->update(request()->all());

        return response()->json($taskType);
    }

    /**
     * API: Menghapus data jenis tugas.
     */
    public function destroy(string $id)
    {
        $taskType = TaskType::findOrFail($id);
        $taskType->delete();
        return response()->json(null, 204);
    }

    /**
     * API: Mengambil jenis tugas berdasarkan kode departemen.
     */
    public function getByDepartment(string $department_code)
    {
        $taskTypes = TaskType::where('departemen', $department_code)
            ->orWhere('departemen', 'UMUM')
            ->orderBy('name_task')
            ->get(['id', 'name_task as text']); // Diubah agar kompatibel dengan Select2

        return response()->json($taskTypes);
    }
}
