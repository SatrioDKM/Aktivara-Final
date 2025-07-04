<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    /**
     * Method untuk menampilkan halaman Blade.
     */
    public function viewPage()
    {
        // Ambil data ruangan untuk dropdown di form
        $rooms = Room::with('floor.building')->where('status', 'active')->get();
        return view('master.assets.index', compact('rooms'));
    }

    /**
     * Menampilkan daftar semua aset.
     */
    public function index()
    {
        // Eager load relasi untuk menampilkan lokasi lengkap dan user yang mengupdate
        $assets = Asset::with(['room.floor.building', 'updater:id,name'])->latest()->get();
        return response()->json($assets);
    }

    /**
     * Menyimpan data aset baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_asset' => 'required|string|max:100',
            'room_id' => 'nullable|exists:rooms,id',
            'category' => 'required|string|max:50',
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number',
            'purchase_date' => 'nullable|date',
            'condition' => 'required|string|max:50',
            'status' => 'required|in:available,in_use,maintenance,disposed',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $asset = Asset::create($data);

        return response()->json($asset->load(['room.floor.building', 'updater:id,name']), 201);
    }

    /**
     * Menampilkan satu data aset spesifik.
     */
    public function show(string $id)
    {
        $asset = Asset::with(['room.floor.building', 'updater:id,name'])->findOrFail($id);
        return response()->json($asset);
    }

    /**
     * Memperbarui data aset yang sudah ada.
     */
    public function update(Request $request, string $id)
    {
        $asset = Asset::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_asset' => 'required|string|max:100',
            'room_id' => 'nullable|exists:rooms,id',
            'category' => 'required|string|max:50',
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number,' . $asset->id,
            'purchase_date' => 'nullable|date',
            'condition' => 'required|string|max:50',
            'status' => 'required|in:available,in_use,maintenance,disposed',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $asset->update($data);

        return response()->json($asset->load(['room.floor.building', 'updater:id,name']));
    }

    /**
     * Menghapus data aset.
     */
    public function destroy(string $id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return response()->json(null, 204);
    }
}
