<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use App\Notifications\NewTaskAvailable; // Import class notifikasi
use Illuminate\Http\JsonResponse; // Ubah ke JsonResponse
use Illuminate\Http\Request; // Import Request
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Import Log
use Illuminate\Support\Facades\Notification; // Import Notification
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class GuestComplaintController extends Controller
{
    /**
     * Menampilkan halaman form keluhan untuk tamu.
     */
    public function create(): View
    {
        $data = [
            'taskTypes' => TaskType::whereIn('departemen', ['HK', 'TK', 'SC', 'UMUM'])->orderBy('name_task')->get(),
        ];
        // Pastikan path view sudah benar
        return view('backend.complaints.guest-form', compact('data'));
    }

    /**
     * API: Menyimpan keluhan dari tamu dan secara otomatis membuat tugas.
     * Mengembalikan JsonResponse, bukan RedirectResponse.
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
            // Kembalikan error validasi sebagai JSON
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request) {
                $superadmin = User::where('role_id', 'SA00')->first();
                $creatorId = $superadmin ? $superadmin->id : 1;

                $newTask = Task::create([
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'task_type_id' => $request->input('task_type_id'),
                    'priority' => 'medium',
                    'status' => 'unassigned',
                    'created_by' => $creatorId,
                ]);

                Complaint::create([
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'reporter_name' => $request->input('reporter_name'),
                    'location_text' => $request->input('location_text'),
                    'status' => 'converted_to_task',
                    'created_by' => $creatorId,
                    'task_id' => $newTask->id,
                ]);

                // Logika Notifikasi (diperbaiki)
                try {
                    $newTask->load('taskType');
                    $departmentCode = $newTask->taskType->departemen;

                    if ($departmentCode && $departmentCode !== 'UMUM') {
                        $staffRole = $departmentCode . '02';
                        $leaderRole = $departmentCode . '01';
                        $recipients = User::whereIn('role_id', [$staffRole, $leaderRole])->get();

                        $guestName = $request->input('reporter_name') . " (Tamu)";

                        if ($recipients->isNotEmpty()) {
                            Notification::send($recipients, new NewTaskAvailable($newTask, $guestName));
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim notifikasi dari GuestComplaint: ' . $e->getMessage());
                }
            });

            // Kembalikan pesan sukses sebagai JSON
            return response()->json(['message' => 'Terima kasih! Laporan Anda telah berhasil dikirim dan akan segera kami proses.'], 201);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan Laporan Tamu: ' . $e->getMessage());
            // Kembalikan error server sebagai JSON
            return response()->json(['message' => 'Terjadi kesalahan pada server. Mohon coba lagi.'], 500);
        }
    }
}
