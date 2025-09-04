<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class StockManagementController extends Controller
{
    /**
     * Menampilkan halaman Blade untuk Manajemen Stok.
     * (INI YANG DIPERBAIKI)
     */
    public function viewPage()
    {
        // Ambil data awal (halaman pertama) untuk dikirim ke view
        // agar variabel $stocks selalu ada untuk pagination links.
        $stocks = Asset::where('asset_type', 'consumable')
            ->orderBy('name_asset')
            ->paginate(15);

        return view('stock.index', compact('stocks'));
    }

    /**
     * API: Mengambil daftar barang habis pakai dengan filter.
     */
    public function index(Request $request)
    {
        $query = Asset::where('asset_type', 'consumable');

        // Filter untuk hanya menampilkan stok menipis
        $query->when($request->boolean('low_stock_only'), function ($q) {
            return $q->whereRaw('current_stock <= minimum_stock')->where('minimum_stock', '>', 0);
        });

        // Filter pencarian
        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('name_asset', 'like', '%' . $request->search . '%');
        });

        $stocks = $query->orderBy('name_asset')->paginate(15);

        return response()->json($stocks);
    }

    /**
     * API: Memperbarui stok minimum untuk satu aset.
     */
    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'minimum_stock' => 'required|integer|min:0',
        ]);

        if ($asset->asset_type !== 'consumable') {
            return response()->json(['message' => 'Hanya stok minimum barang habis pakai yang bisa diubah.'], 400);
        }

        $asset->update([
            'minimum_stock' => $request->minimum_stock,
            'updated_by' => Auth::id(),
        ]);

        return response()->json($asset);
    }
}
