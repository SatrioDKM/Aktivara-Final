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
                    // $newTask sudah dibuat sebelumnya
                    $newTask->load('taskType'); // Pastikan relasi taskType di-load
                    $departmentCode = $newTask->taskType->departemen; // Ambil kode departemen

                    // Hanya kirim notifikasi jika ada kode departemen spesifik (bukan UMUM)
                    if ($departmentCode && $departmentCode !== 'UMUM') {
                        $leaderRole = $departmentCode . '01'; // Targetkan hanya Leader
                        $recipients = User::where('role_id', $leaderRole)->get(); // Cari Leader

                        $guestName = $request->input('reporter_name') . " (Tamu)";

                        if ($recipients->isNotEmpty()) {
                            // Kirim notifikasi HANYA ke Leader
                            Notification::send($recipients, new NewTaskAvailable($newTask, $guestName));
                        } else {
                            Log::warning("Tidak ditemukan Leader ({$leaderRole}) untuk notifikasi tugas tamu #{$newTask->id}");
                            // Opsional: Kirim ke Superadmin sebagai fallback jika Leader tidak ada
                            $superadmin = User::where('role_id', 'SA00')->get();
                            if ($superadmin->isNotEmpty()) {
                                Notification::send($superadmin, new NewTaskAvailable($newTask, $guestName));
                                Log::info("Notifikasi tugas tamu #{$newTask->id} dikirim ke Superadmin sebagai fallback.");
                            }
                        }
                    } else {
                        // Jika departemen UMUM atau tidak ada, mungkin tidak perlu notifikasi,
                        // atau kirim ke peran tertentu (misal: Manager/Admin)
                        Log::info("Tugas tamu #{$newTask->id} tidak memiliki departemen spesifik, notifikasi tidak dikirim ke leader departemen.");
                        // Opsional: Kirim ke Manager/Admin jika tugas UMUM
                        $managerAdmin = User::whereIn('role_id', ['MG00', 'SA00'])->get();
                        if ($managerAdmin->isNotEmpty()) {
                            Notification::send($managerAdmin, new NewTaskAvailable($newTask, $request->input('reporter_name') . " (Tamu)"));
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
