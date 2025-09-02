<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Task;
use App\Models\Asset;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    /**
     * Menampilkan halaman Blade untuk Laporan/Keluhan.
     */
    public function viewPage()
    {
        // Ambil data untuk dropdown di form
        $rooms = Room::with('floor.building')->where('status', 'active')->get();
        $assets = Asset::where('status', '!=', 'disposed')->orderBy('name_asset')->get();
        return view('complaints.index', compact('rooms', 'assets'));
    }

    /**
     * API: Mengambil semua data keluhan.
     */
    public function index()
    {
        $complaints = Complaint::with(['creator:id,name', 'generatedTask:id,title'])->latest()->get();
        return response()->json($complaints);
    }

    /**
     * API: Menyimpan keluhan baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
     * API: Mengonversi keluhan menjadi tugas.
     */
    public function convertToTask(Request $request, string $id)
    {
        $complaint = Complaint::findOrFail($id);

        // Pastikan laporan belum pernah dikonversi
        if ($complaint->status !== 'open') {
            return response()->json(['message' => 'Laporan ini sudah diproses.'], 409); // 409 Conflict
        }

        $validator = Validator::make($request->all(), [
            'task_type_id' => 'required|exists:task_types,id',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request, $complaint) {
                // 1. Buat tugas baru dari data keluhan
                $newTask = Task::create([
                    'title' => $complaint->title,
                    'description' => $complaint->description,
                    'task_type_id' => $request->task_type_id,
                    'priority' => $request->priority,
                    'room_id' => $complaint->room_id,
                    'asset_id' => $complaint->asset_id,
                    'status' => 'unassigned',
                    'created_by' => Auth::id(),
                ]);

                // 2. Update status keluhan dan tautkan ke tugas baru
                $complaint->update([
                    'status' => 'converted_to_task',
                    'task_id' => $newTask->id,
                ]);
            });

            return response()->json(['message' => 'Laporan berhasil dikonversi menjadi tugas.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengonversi laporan: ' . $e->getMessage()], 500);
        }
    }
}
