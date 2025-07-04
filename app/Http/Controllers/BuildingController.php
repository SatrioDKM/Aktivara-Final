<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BuildingController extends Controller
{
    /**
     * Method untuk menampilkan halaman Blade.
     * Ini dipanggil dari routes/web.php
     */
    public function viewPage()
    {
        // Nama view mengikuti struktur folder yang rapi
        return view('master.buildings.index');
    }

    /**
     * Menampilkan daftar semua gedung.
     * GET /api/buildings
     */
    public function index()
    {
        // Mengambil semua data gedung, diurutkan berdasarkan yang terbaru
        // Eager load 'creator' untuk menampilkan nama user yang membuat
        $buildings = Building::with('creator:id,name')->latest()->get();

        // Mengembalikan data dalam format JSON
        return response()->json($buildings);
    }

    /**
     * Menyimpan data gedung baru.
     * POST /api/buildings
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name_building' => 'required|string|max:100|unique:buildings,name_building',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Membuat data gedung baru
        $building = Building::create([
            'name_building' => $request->name_building,
            'address' => $request->address,
            'status' => $request->status,
            'created_by' => Auth::id(), // Mengambil ID user yang sedang login
        ]);

        // Mengembalikan data yang baru dibuat dengan status 201 (Created)
        return response()->json($building->load('creator:id,name'), 201);
    }

    /**
     * Menampilkan satu data gedung spesifik.
     * GET /api/buildings/{id}
     */
    public function show(string $id)
    {
        // Mencari gedung berdasarkan ID, jika tidak ketemu akan menampilkan error 404
        $building = Building::findOrFail($id);

        // Mengembalikan data gedung yang ditemukan dalam format JSON
        return response()->json($building->load('creator:id,name'));
    }

    /**
     * Memperbarui data gedung yang sudah ada.
     * PUT /api/buildings/{id}
     */
    public function update(Request $request, string $id)
    {
        // Mencari gedung berdasarkan ID terlebih dahulu
        $building = Building::findOrFail($id);

        // Validasi input
        $validator = Validator::make($request->all(), [
            // Pastikan nama unik, kecuali untuk dirinya sendiri
            'name_building' => 'required|string|max:100|unique:buildings,name_building,' . $building->id,
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Memperbarui data
        $building->update($request->all());

        // Mengembalikan data yang sudah diperbarui
        return response()->json($building->load('creator:id,name'));
    }

    /**
     * Menghapus data gedung.
     * DELETE /api/buildings/{id}
     */
    public function destroy(string $id)
    {
        // Mencari gedung berdasarkan ID
        $building = Building::findOrFail($id);

        // Hapus data
        $building->delete();

        // Kembalikan respons kosong dengan status 204 (No Content)
        return response()->json(null, 204);
    }
}
