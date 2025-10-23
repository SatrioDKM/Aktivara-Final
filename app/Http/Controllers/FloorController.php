<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Building;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FloorController extends Controller
{
    /**
     * Menampilkan halaman daftar lantai (index).
     */
    public function viewPage(): View
    {
        $data = [
            'buildings' => Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']),
        ];
        return view('backend.master.floors.index', compact('data'));
    }

    /**
     * Menampilkan halaman formulir tambah lantai.
     */
    public function create(): View
    {
        $data = [
            'buildings' => Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']),
        ];
        return view('backend.master.floors.create', compact('data'));
    }

    /**
     * Menampilkan halaman detail lantai.
     */
    public function showPage(string $id): View
    {
        $data = [
            'floor' => Floor::with(['building:id,name_building', 'creator:id,name', 'rooms'])->findOrFail($id)
        ];
        return view('backend.master.floors.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir edit lantai.
     */
    public function edit(string $id): View
    {
        $data = [
            'floor' => Floor::findOrFail($id),
            'buildings' => Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']),
        ];
        return view('backend.master.floors.edit', compact('data'));
    }

    // ===================================================================
    // API METHODS
    // ===================================================================

    /**
     * API: Mengambil daftar lantai dengan paginasi dan filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Floor::with('building:id,name_building');

        $query->when($request->input('search'), fn($q, $search) => $q->where('name_floor', 'like', "%{$search}%"));
        $query->when($request->input('building'), fn($q, $buildingId) => $q->where('building_id', $buildingId));

        $floors = $query->latest()->paginate($request->input('perPage', 10));
        return response()->json($floors);
    }

    /**
     * === FUNGSI BARU YANG DITAMBAHKAN ===
     * API: Mengambil SEMUA daftar lantai (flat list) untuk dropdown.
     */
    public function listAll(Request $request): JsonResponse
    {
        $query = Floor::query()->where('status', 'active')->orderBy('name_floor');

        // Filter berdasarkan building_id jika ada
        $query->when($request->input('building_id'), function ($q, $buildingId) {
            $q->where('building_id', $buildingId);
        });

        // PENTING: Halaman dropdown menggunakan ->get()
        return response()->json($query->get(['id', 'name_floor']));
    }

    /**
     * API: Menyimpan data lantai baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name_floor' => 'required|string|max:50',
            'building_id' => 'required|exists:buildings,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $floor = Floor::create([
            'name_floor' => $request->input('name_floor'),
            'building_id' => $request->input('building_id'),
            'status' => $request->input('status'),
            'created_by' => Auth::id(),
        ]);

        return response()->json($floor->load(['building:id,name_building', 'creator:id,name']), 201);
    }

    /**
     * API: Menampilkan satu data lantai spesifik.
     */
    public function show(string $id): JsonResponse
    {
        $floor = Floor::with(['building:id,name_building', 'creator:id,name'])->findOrFail($id);
        return response()->json($floor);
    }

    /**
     * API: Memperbarui data lantai yang sudah ada.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $floor = Floor::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_floor' => 'required|string|max:50',
            'building_id' => 'required|exists:buildings,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $floor->update($request->all());

        return response()->json($floor->load(['building:id,name_building', 'creator:id,name']));
    }

    /**
     * API: Menghapus data lantai.
     */
    public function destroy(string $id): JsonResponse
    {
        $floor = Floor::findOrFail($id);
        $floor->delete();
        return response()->json(['message' => 'Data lantai berhasil dihapus.'], 200);
    }
}
