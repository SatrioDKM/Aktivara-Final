<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class StockManagementController extends Controller
{
    /**
     * Menampilkan halaman Blade untuk Manajemen Stok.
     */
    public function viewPage(): View
    {
        $data = [
            'stocks' => Asset::where('asset_type', 'consumable')
                ->orderBy('name_asset')
                ->paginate(15),
        ];
        return view('backend.stock.index', compact('data'));
    }

    /**
     * API: Mengambil daftar barang habis pakai dengan filter.
     */
    public function index()
    {
        $query = Asset::where('asset_type', 'consumable');

        // Filter untuk hanya menampilkan stok menipis
        $query->when(filter_var(request('low_stock_only'), FILTER_VALIDATE_BOOLEAN), function ($q) {
            return $q->whereRaw('current_stock <= minimum_stock')->where('minimum_stock', '>', 0);
        });

        // Filter pencarian
        if (request('search', '')) {
            $query->where('name_asset', 'like', '%' . request('search') . '%');
        }

        $stocks = $query->orderBy('name_asset')->paginate(request('perPage', 15));

        return response()->json($stocks);
    }

    /**
     * API: Memperbarui stok minimum untuk satu aset.
     */
    public function update(string $id)
    {
        $asset = Asset::findOrFail($id);

        $validator = Validator::make(request()->all(), [
            'minimum_stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($asset->asset_type !== 'consumable') {
            return response()->json(['message' => 'Hanya stok minimum barang habis pakai yang bisa diubah.'], 400);
        }

        $asset->update([
            'minimum_stock' => request('minimum_stock'),
            'updated_by' => Auth::id(),
        ]);

        return response()->json($asset);
    }
}
