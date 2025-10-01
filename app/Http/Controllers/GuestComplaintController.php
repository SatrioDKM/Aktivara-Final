<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; // Menggunakan Request class
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class GuestComplaintController extends Controller
{
    /**
     * Menampilkan halaman form keluhan untuk tamu.
     */
    public function create(): View
    {
        // Query sudah dioptimalkan dan tidak menyebabkan N+1
        $data = [
            'taskTypes' => TaskType::whereIn('departemen', ['HK', 'TK', 'SC', 'UMUM'])
                ->orderBy('name_task')
                ->get(),
        ];
        return view('backend.complaints.guest-form', compact('data'));
    }

    /**
     * Menyimpan keluhan dari tamu via API dan secara otomatis membuat tugas.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'reporter_name' => 'required|string|max:100',
            'location_text' => 'required|string|max:255',
            'task_type_id' => 'required|exists:task_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Menggunakan DB::transaction untuk memastikan integritas data
            $complaint = DB::transaction(function () use ($request) {
                // Mengambil user Superadmin sebagai pencatat default
                $superadmin = User::where('role_id', 'SA00')->first();
                $creatorId = $superadmin ? $superadmin->id : 1; // Fallback ke ID 1 jika tidak ada

                // 1. Buat tugas baru secara langsung
                $newTask = Task::create([
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'task_type_id' => $request->input('task_type_id'),
                    'priority' => 'medium', // Default priority untuk laporan tamu
                    'status' => 'unassigned',
                    'created_by' => $creatorId,
                ]);

                // 2. Buat record di tabel keluhan untuk arsip
                return Complaint::create([
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'reporter_name' => $request->input('reporter_name'),
                    'location_text' => $request->input('location_text'),
                    'status' => 'converted_to_task',
                    'created_by' => $creatorId,
                    'task_id' => $newTask->id,
                ]);
            });

            return response()->json([
                'message' => 'Terima kasih! Laporan Anda telah berhasil dikirim dan akan segera kami proses.'
            ], 201); // 201 Created

        } catch (\Exception $e) {
            // Logging error untuk debugging
            Log::error('Gagal membuat laporan tamu: ' . $e->getMessage());

            return response()->json([
                'message' => 'Terjadi kesalahan pada server. Mohon coba lagi nanti.'
            ], 500);
        }
    }
}
