<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\PackingList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssetHistoryController extends Controller
{
    /**
     * Menampilkan halaman riwayat aset.
     */
    public function viewPage(): View
    {
        $data = []; // Kirim data kosong sesuai standar
        return view('backend.asset_history.index', compact('data'));
    }

    /**
     * API: Mengambil data riwayat aset (masuk/keluar) dengan filter dan paginasi.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('perPage', 15);
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type'); // 'in' atau 'out'

        // Query untuk Barang Masuk (dari tabel assets)
        $inQuery = Asset::select(
            'assets.id as asset_id',
            'assets.name_asset',
            'assets.serial_number',
            'assets.created_at as timestamp',
            DB::raw("'in' as type"), // Tandai sebagai 'in'
            'users.name as user_name', // Nama user yang membuat aset
            DB::raw("NULL as document_number"), // Kolom packing list null
            DB::raw("NULL as recipient_name") // Kolom packing list null
        )
            ->join('users', 'assets.created_by', '=', 'users.id') // Join ke user pembuat
            ->where('assets.asset_type', 'consumable'); // Fokus pada barang habis pakai jika perlu

        // Query untuk Barang Keluar (dari tabel packing_lists)
        $outQuery = DB::table('asset_packing_list')
            ->select(
                'asset_packing_list.asset_id',
                'assets.name_asset',
                'assets.serial_number',
                'packing_lists.created_at as timestamp',
                DB::raw("'out' as type"), // Tandai sebagai 'out'
                'users.name as user_name', // Nama user yang membuat packing list
                'packing_lists.document_number', // Nomor dokumen packing list
                'packing_lists.recipient_name' // Nama penerima
            )
            ->join('packing_lists', 'asset_packing_list.packing_list_id', '=', 'packing_lists.id')
            ->join('assets', 'asset_packing_list.asset_id', '=', 'assets.id')
            ->join('users', 'packing_lists.created_by', '=', 'users.id') // Join ke user pembuat packing list
            ->where('assets.asset_type', 'consumable'); // Fokus pada barang habis pakai jika perlu

        // Terapkan filter tanggal jika ada
        if ($startDate) {
            $inQuery->whereDate('assets.created_at', '>=', $startDate);
            $outQuery->whereDate('packing_lists.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $inQuery->whereDate('assets.created_at', '<=', $endDate);
            $outQuery->whereDate('packing_lists.created_at', '<=', $endDate);
        }

        // Terapkan filter pencarian nama aset jika ada
        if ($search) {
            $inQuery->where('assets.name_asset', 'like', "%{$search}%");
            $outQuery->where('assets.name_asset', 'like', "%{$search}%");
        }

        // Gabungkan query berdasarkan filter tipe (jika ada)
        if ($type === 'in') {
            $finalQuery = $inQuery;
        } elseif ($type === 'out') {
            $finalQuery = $outQuery;
        } else {
            // Jika tidak ada filter tipe, gabungkan keduanya
            $finalQuery = $inQuery->unionAll($outQuery);
        }

        // Urutkan berdasarkan waktu dan paginasi
        $history = $finalQuery->orderBy('timestamp', 'desc')->paginate($perPage);

        return response()->json($history);
    }
}
