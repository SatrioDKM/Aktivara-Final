<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\AssetsExport;
use Illuminate\Routing\Controller;
use App\Exports\DailyReportsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{

    /**
     * Menampilkan halaman utama untuk ekspor data.
     * (INI METODE BARU)
     */
    public function viewPage()
    {
        return view('export.index');
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
     * Memicu download file Excel untuk data laporan harian.
     */
    public function exportDailyReports()
    {
        $fileName = 'laporan-harian-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new DailyReportsExport, $fileName);
    }
}
