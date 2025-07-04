<?php

namespace App\Http\Controllers;

use App\Models\TaskType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskTypeController extends Controller
{
    /**
     * Method untuk menampilkan halaman Blade.
     */
    public function viewPage()
    {
        return view('master.task_types.index');
    }

    /**
     * Menampilkan daftar semua jenis tugas.
     */
    public function index()
    {
        $taskTypes = TaskType::latest()->get();
        return response()->json($taskTypes);
    }

    /**
     * Menyimpan data jenis tugas baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_task' => 'required|string|max:100|unique:task_types,name_task',
            'departemen' => 'required|string|max:50',
            'priority_level' => 'required|in:low,medium,high,critical',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $taskType = TaskType::create($request->all());

        return response()->json($taskType, 201);
    }

    /**
     * Menampilkan satu data jenis tugas spesifik.
     */
    public function show(string $id)
    {
        $taskType = TaskType::findOrFail($id);
        return response()->json($taskType);
    }

    /**
     * Memperbarui data jenis tugas yang sudah ada.
     */
    public function update(Request $request, string $id)
    {
        $taskType = TaskType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_task' => 'required|string|max:100|unique:task_types,name_task,' . $taskType->id,
            'departemen' => 'required|string|max:50',
            'priority_level' => 'required|in:low,medium,high,critical',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $taskType->update($request->all());

        return response()->json($taskType);
    }

    /**
     * Menghapus data jenis tugas.
     */
    public function destroy(string $id)
    {
        $taskType = TaskType::findOrFail($id);
        $taskType->delete();

        return response()->json(null, 204);
    }
}
