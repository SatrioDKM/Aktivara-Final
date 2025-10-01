<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
        return view('backend.complaints.guest-form', compact('data'));
    }

    /**
     * Menyimpan keluhan dari tamu dan secara otomatis membuat tugas.
     */
    public function store(): RedirectResponse
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'reporter_name' => 'required|string|max:100',
            'location_text' => 'required|string|max:255',
            'task_type_id' => 'required|exists:task_types,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () {
                // Karena ini dari tamu, tidak ada 'created_by'
                // Kita bisa menggunakan user Superadmin sebagai default jika diperlukan
                $superadmin = User::where('role_id', 'SA00')->first();

                // 1. Buat tugas baru secara langsung
                $newTask = Task::create([
                    'title' => request('title'),
                    'description' => request('description'),
                    'task_type_id' => request('task_type_id'),
                    'priority' => 'medium', // Default priority untuk laporan tamu
                    'status' => 'unassigned',
                    'created_by' => $superadmin ? $superadmin->id : 1, // Dibuat oleh sistem/superadmin
                ]);

                // 2. Buat record di tabel keluhan untuk arsip
                Complaint::create([
                    'title' => request('title'),
                    'description' => request('description'),
                    'reporter_name' => request('reporter_name'),
                    'location_text' => request('location_text'),
                    'status' => 'converted_to_task', // Langsung dianggap sudah jadi tugas
                    'created_by' => $superadmin ? $superadmin->id : 1,
                    'task_id' => $newTask->id, // Tautkan ke tugas yang baru dibuat
                ]);
            });

            return redirect()->back()->with('success', 'Terima kasih! Laporan Anda telah berhasil dikirim dan akan segera kami proses.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan. Mohon coba lagi.')->withInput();
        }
    }
}
