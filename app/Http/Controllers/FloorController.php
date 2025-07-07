<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Building; // Kita butuh ini untuk validasi

class FloorController extends Controller
{
    /**
     * Method untuk menampilkan halaman Blade.
     */
    public function viewPage()
    {
        // Mengambil data gedung yang aktif untuk dilempar ke view,
        // agar bisa digunakan untuk mengisi dropdown pada form.
        $buildings = Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']);

        // Nama view mengikuti struktur folder yang rapi
        return view('master.floors.index', compact('buildings'));
    }

    /**
     * Menampilkan daftar semua lantai.
     * GET /api/floors
     */
    public function index()
    {
        // Eager load relasi 'building' dan 'creator' untuk mencegah N+1 problem
        $floors = Floor::with(['building:id,name_building', 'creator:id,name'])->latest()->get();

        return response()->json($floors);
    }

    /**
     * Menyimpan data lantai baru.
     * POST /api/floors
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_floor' => 'required|string|max:50',
            'building_id' => 'required|exists:buildings,id', // Pastikan building_id valid
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $floor = Floor::create([
            'name_floor' => $request->name_floor,
            'building_id' => $request->building_id,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        // Muat relasi setelah dibuat agar data yang dikembalikan lengkap
        return response()->json($floor->load(['building:id,name_building', 'creator:id,name']), 201);
    }

    /**
     * Menampilkan satu data lantai spesifik.
     * GET /api/floors/{id}
     */
    public function show(string $id)
    {
        $floor = Floor::with(['building:id,name_building', 'creator:id,name'])->findOrFail($id);
        return response()->json($floor);
    }

    /**
     * Memperbarui data lantai yang sudah ada.
     * PUT /api/floors/{id}
     */
    public function update(Request $request, string $id)
    {
        $floor = Floor::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_floor' => 'required|string|max:50',
            'building_id' => 'required|exists:buildings,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $floor->update($request->all());

        return response()->json($floor->load(['building:id,name_building', 'creator:id,name']));
    }

    /**
     * Menghapus data lantai.
     * DELETE /api/floors/{id}
     */
    public function destroy(string $id)
    {
        $floor = Floor::findOrFail($id);
        $floor->delete();

        return response()->json(null, 204);
    }
}
