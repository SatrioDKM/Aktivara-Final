<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use App\Models\ReportAttachment;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ReportSubmitted;

class DailyReportController extends Controller
{
    /**
     * Menyimpan laporan baru dari staff via API.
     */
    public function storeApi(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak berhak melaporkan tugas ini.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:10240'
        ]);

        try {
            $report = DB::transaction(function () use ($validated, $request, $task) {
                $newReport = DailyReport::create([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'status' => 'submitted',
                ]);

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $path = $file->store('attachments', 'public');
                        ReportAttachment::create([
                            'daily_report_id' => $newReport->id,
                            'file_path' => $path,
                            'file_type' => $file->getClientMimeType(),
                        ]);
                    }
                }

                $task->update(['status' => 'pending_review']);

                // --- KIRIM NOTIFIKASI LAPORAN DIKIRIM ---
                // Kirim notifikasi ke pembuat tugas (Leader)
                $task->creator->notify(new ReportSubmitted($task, Auth::user()));
                // -----------------------------------------

                return $newReport;
            });

            return response()->json($report->load('attachments'), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengirim laporan.'], 500);
        }
    }
}
