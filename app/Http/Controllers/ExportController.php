<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\AssetsExport;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
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
}
