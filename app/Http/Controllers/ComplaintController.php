<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Complaint;
use App\Models\Room;
use App\Models\Task;
use App\Models\TaskType;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * Menampilkan halaman utama (index).
     */
    public function viewPage(): View
    {
        $data = [
            'taskTypes' => TaskType::orderBy('name_task')->get(['id', 'name_task', 'departemen']),
        ];
        return view('backend.complaints.index', compact('data'));
    }

    /**
     * Menampilkan halaman formulir tambah.
     */
    public function create(): View
    {
        $data = [
            'rooms' => Room::with('floor.building')->where('status', 'active')->get(),
            'assets' => Asset::where('status', '!=', 'disposed')->orderBy('name_asset')->get(),
        ];
        return view('backend.complaints.create', compact('data'));
    }

    /**
     * Menampilkan halaman detail (show).
     */
    public function showPage(string $id): View
    {
        $data = [
            'complaint' => Complaint::with(['creator:id,name', 'room.floor.building', 'asset', 'generatedTask:id,title'])->findOrFail($id)
        ];
        return view('backend.complaints.show', compact('data'));
    }

    // ===================================================================
    // API METHODS
    // ===================================================================

    /**
     * API: Mengambil semua data keluhan dengan paginasi dan filter.
     */
    public function index()
    {
        $query = Complaint::with(['creator:id,name', 'generatedTask:id,title']);

        if (request('search', '')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('reporter_name', 'like', "%{$search}%");
            });
        }

        if (request('status', '')) {
            $query->where('status', request('status'));
        }

        $complaints = $query->latest()->paginate(request('perPage', 10));
        return response()->json($complaints);
    }

    /**
     * API: Menyimpan keluhan baru.
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'reporter_name' => 'required|string|max:100',
            'location_text' => 'required|string|max:255',
            'room_id' => 'nullable|exists:rooms,id',
            'asset_id' => 'nullable|exists:assets,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['created_by'] = Auth::id();
        $data['status'] = 'open';

        $complaint = Complaint::create($data);
        return response()->json($complaint, 201);
    }

    /**
     * API: Menampilkan satu data keluhan spesifik.
     */
    public function show(string $id)
    {
        $complaint = Complaint::with(['creator:id,name', 'generatedTask:id,title'])->findOrFail($id);
        return response()->json($complaint);
    }

    /**
     * API: Menghapus data keluhan.
     */
    public function destroy(string $id)
    {
        $complaint = Complaint::where('status', 'open')->findOrFail($id);
        $complaint->delete();
        return response()->json(null, 204);
    }

    /**
     * API: Mengonversi keluhan menjadi tugas.
     */
    public function convertToTask(string $id)
    {
        $complaint = Complaint::findOrFail($id);

        if ($complaint->status !== 'open') {
            return response()->json(['message' => 'Laporan ini sudah diproses.'], 409);
        }

        $validator = Validator::make(request()->all(), [
            'task_type_id' => 'required|exists:task_types,id',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($complaint) {
                $newTask = Task::create([
                    'title' => $complaint->title,
                    'description' => $complaint->description,
                    'task_type_id' => request('task_type_id'),
                    'priority' => request('priority'),
                    'room_id' => $complaint->room_id,
                    'asset_id' => $complaint->asset_id,
                    'status' => 'unassigned',
                    'created_by' => Auth::id(),
                ]);

                $complaint->update([
                    'status' => 'converted_to_task',
                    'task_id' => $newTask->id,
                ]);
            });

            return response()->json(['message' => 'Laporan berhasil dikonversi menjadi tugas.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
