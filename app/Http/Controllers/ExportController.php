<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Exports\AssetsExport;
use App\Exports\TaskHistoryExport;
use Illuminate\Routing\Controller;
use App\Exports\DailyReportsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Menampilkan halaman utama untuk menu ekspor data.
     * Metode ini hanya me-render view tanpa mengirim data.
     */
    public function viewPage(): View
    {
        // Path view diperbarui ke 'backend.export.index'
        return view('backend.export.index');
    }

    /**
     * Memicu download file Excel untuk data aset.
     */
    public function exportAssets()
    {
        // Membuat nama file yang dinamis dengan tanggal saat ini
        $fileName = 'daftar-aset-' . now()->format('Y-m-d') . '.xlsx';

        // Menggunakan library untuk mendownload file
        return Excel::download(new AssetsExport, $fileName);
    }

    /**
     * Memicu download file Excel untuk data riwayat & laporan tugas.
     * Nama fungsi disesuaikan agar lebih relevan.
     */
    public function exportTaskHistory()
    {
        // Menggunakan class export yang baru dan benar
        $fileName = 'riwayat-tugas-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new TaskHistoryExport, $fileName);
    }
}
