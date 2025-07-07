<?php

namespace App\Exports;

use App\Models\DailyReport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DailyReportsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * Mengambil data laporan harian dari database.
     * Eager loading digunakan untuk efisiensi query.
     */
    public function query()
    {
        return DailyReport::query()->with(['task', 'user']);
    }

    /**
     * Mendefinisikan judul untuk setiap kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'ID Laporan',
            'Judul Laporan',
            'Deskripsi Laporan',
            'Tugas Terkait',
            'ID Tugas',
            'Dilaporkan Oleh',
            'Tanggal Laporan',
        ];
    }

    /**
     * Memetakan data dari setiap model DailyReport ke dalam format baris Excel.
     * @param \App\Models\DailyReport $report
     */
    public function map($report): array
    {
        return [
            $report->id,
            $report->title,
            $report->description,
            $report->task->title ?? 'N/A', // Handle jika tugas terkait sudah dihapus
            $report->task_id,
            $report->user->name ?? 'Pengguna Dihapus', // Handle jika user terkait sudah dihapus
            $report->created_at->format('d-m-Y H:i:s'),
        ];
    }
}
