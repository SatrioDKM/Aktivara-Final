<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; // <-- Import Request class
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
        return view('backend.stock.index');
    }

    /**
     * API: Mengambil daftar barang habis pakai dengan filter dan paginasi.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Asset::where('asset_type', 'consumable');

        // Filter untuk hanya menampilkan stok menipis
        $query->when($request->boolean('low_stock_only'), function ($q) {
            return $q->whereRaw('current_stock <= minimum_stock')->where('minimum_stock', '>', 0);
        });

        // Filter pencarian
        $query->when($request->input('search'), function ($q, $search) {
            $q->where('name_asset', 'like', '%' . $search . '%');
        });

        $stocks = $query->orderBy('name_asset')->paginate($request->input('perPage', 15));

        return response()->json($stocks);
    }

    /**
     * API: Memperbarui stok minimum untuk satu aset.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $asset = Asset::findOrFail($id);

        // dd($request->input('minimum_stock'));

        // --- PERBAIKAN 1: Gunakan $request->all() ---
        $validator = Validator::make($request->all(), [
            'minimum_stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($asset->asset_type !== 'consumable') {
            return response()->json(['message' => 'Hanya stok minimum barang habis pakai yang bisa diubah.'], 400);
        }

        $asset->update([
            // --- PERBAIKAN 2: Gunakan $request->input() ---
            'minimum_stock' => $request->input('minimum_stock'),
            'updated_by' => Auth::id(),
        ]);

        return response()->json($asset);
    }
}
