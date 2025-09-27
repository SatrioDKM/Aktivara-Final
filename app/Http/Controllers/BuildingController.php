<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class BuildingController extends Controller
{
    /**
     * Menampilkan halaman utama (index).
     */
    public function viewPage(): View
    {
        return view('backend.master.buildings.index');
    }

    /**
     * Menampilkan halaman formulir tambah.
     */
    public function create(): View
    {
        return view('backend.master.buildings.create');
    }

    /**
     * Menampilkan halaman detail (show).
     */
    public function showPage(string $id): View
    {
        $data = [
            'building' => Building::with(['creator:id,name', 'floors.rooms'])->findOrFail($id)
        ];
        return view('backend.master.buildings.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir edit.
     */
    public function edit(string $id): View
    {
        $data = [
            'building' => Building::findOrFail($id)
        ];
        return view('backend.master.buildings.edit', compact('data'));
    }

    // ===================================================================
    // API METHODS
    // ===================================================================

    /**
     * API: Menampilkan daftar semua gedung dengan paginasi dan filter.
     */
    public function index()
    {
        $query = Building::with('creator:id,name');

        // Terapkan filter pencarian
        if (request('search', '')) {
            $query->where('name_building', 'like', '%' . request('search') . '%');
        }

        $buildings = $query->latest()->paginate(request('perPage', 10));

        return response()->json($buildings);
    }

    /**
     * API: Menyimpan data gedung baru.
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name_building' => 'required|string|max:100|unique:buildings,name_building',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $building = Building::create([
            'name_building' => request('name_building'),
            'address' => request('address'),
            'status' => request('status'),
            'created_by' => Auth::id(),
        ]);

        return response()->json($building->load('creator:id,name'), 201);
    }

    /**
     * API: Menampilkan satu data gedung spesifik.
     */
    public function show(string $id)
    {
        $building = Building::with('creator:id,name')->findOrFail($id);
        return response()->json($building);
    }

    /**
     * API: Memperbarui data gedung yang sudah ada.
     */
    public function update(string $id)
    {
        $building = Building::findOrFail($id);

        $validator = Validator::make(request()->all(), [
            'name_building' => 'required|string|max:100|unique:buildings,name_building,' . $building->id,
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $building->update(request()->all());

        return response()->json($building->load('creator:id,name'));
    }

    /**
     * API: Menghapus data gedung.
     */
    public function destroy(string $id)
    {
        $building = Building::findOrFail($id);
        $building->delete();
        return response()->json(null, 204);
    }
}
