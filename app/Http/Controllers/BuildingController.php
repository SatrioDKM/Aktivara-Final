<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class BuildingController extends Controller
{
    /**
     * Menampilkan halaman daftar gedung (index).
     */
    public function viewPage(): View
    {
        return view('backend.master.buildings.index');
    }

    /**
     * Menampilkan halaman formulir tambah gedung.
     */
    public function create(): View
    {
        return view('backend.master.buildings.create');
    }

    /**
     * Menampilkan halaman detail gedung.
     */
    public function showPage(string $id): View
    {
        $data = [
            'building' => Building::with(['creator:id,name', 'floors.rooms'])->findOrFail($id)
        ];
        return view('backend.master.buildings.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir edit gedung.
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
    public function index(Request $request): JsonResponse
    {
        $query = Building::with('creator:id,name');

        $query->when($request->input('search'), function ($q, $search) {
            $q->where('name_building', 'like', '%' . $search . '%');
        });

        $buildings = $query->latest()->paginate($request->input('perPage', 10));
        return response()->json($buildings);
    }

    /**
     * API: Menyimpan data gedung baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name_building' => 'required|string|max:100|unique:buildings,name_building',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $building = Building::create([
            'name_building' => $request->input('name_building'),
            'address' => $request->input('address'),
            'status' => $request->input('status'),
            'created_by' => Auth::id(),
        ]);

        return response()->json($building->load('creator:id,name'), 201);
    }

    /**
     * API: Menampilkan satu data gedung spesifik.
     */
    public function show(string $id): JsonResponse
    {
        $building = Building::with('creator:id,name')->findOrFail($id);
        return response()->json($building);
    }

    /**
     * API: Memperbarui data gedung yang sudah ada.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $building = Building::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_building' => 'required|string|max:100|unique:buildings,name_building,' . $building->id,
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $building->update($request->all());

        return response()->json($building->load('creator:id,name'));
    }

    /**
     * API: Menghapus data gedung.
     */
    public function destroy(string $id): JsonResponse
    {
        $building = Building::findOrFail($id);
        $building->delete();
        return response()->json(['message' => 'Data gedung berhasil dihapus.'], 200);
    }
}
