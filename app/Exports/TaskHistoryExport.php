<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TaskHistoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * Mengambil data riwayat tugas dari database.
     * Menggunakan eager loading untuk efisiensi.
     */
    public function query()
    {
        // Memuat semua relasi yang dibutuhkan untuk laporan
        return Task::query()->with([
            'assignee:id,name',
            'creator:id,name',
            'taskType:id,name_task',
            'room:id,name_room'
        ])->whereNotNull('user_id'); // Hanya tugas yang sudah dikerjakan
    }

    /**
     * Mendefinisikan judul untuk setiap kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'ID Tugas',
            'Judul Tugas',
            'Jenis Tugas',
            'Status',
            'Prioritas',
            'Dikerjakan Oleh (Staff)',
            'Dibuat Oleh (Leader)',
            'Lokasi',
            'Deskripsi Laporan',
            'Tanggal Dibuat',
            'Tanggal Terakhir Update',
        ];
    }

    /**
     * Memetakan data dari setiap model Task ke dalam format baris Excel.
     * @param \App\Models\Task $task
     */
    public function map($task): array
    {
        return [
            $task->id,
            $task->title,
            $task->taskType->name_task ?? 'N/A',
            $task->status,
            $task->priority,
            $task->assignee->name ?? 'N/A',
            $task->creator->name ?? 'Sistem',
            $task->room->name_room ?? 'Tidak spesifik',
            $task->report_text, // Mengambil dari kolom report_text
            $task->created_at->format('d-m-Y H:i:s'),
            $task->updated_at->format('d-m-Y H:i:s'),
        ];
    }
}
