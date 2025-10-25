<?php

namespace App\HttpControllers;

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
        // --- PERBAIKAN: Mengirim $data kosong agar sesuai ketentuan ---
        $data = [];
        return view('backend.export.index', compact('data'));
    }

    /**
     * Memicu download file Excel untuk data aset.
     */
    public function exportAssets()
    {
        // Method ini sudah benar.
        // Class AssetsExport (dari file sebelumnya) sudah dicek dan aman dari N+1.
        $fileName = 'daftar-aset-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new AssetsExport, $fileName);
    }

    /**
     * Memicu download file Excel untuk data riwayat & laporan tugas.
     * Nama fungsi disesuaikan agar lebih relevan.
     */
    public function exportTaskHistory()
    {
        // Method ini sudah benar.
        // Class TaskHistoryExport (dari file sebelumnya) sudah dicek dan aman dari N+1.
        $fileName = 'riwayat-tugas-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new TaskHistoryExport, $fileName);
    }
}
