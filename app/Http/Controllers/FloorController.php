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
     * Menampilkan halaman utama (index).
     */
    public function viewPage(): View
    {
        $data = [
            'buildings' => Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']),
        ];
        return view('backend.master.floors.index', compact('data'));
    }

    /**
     * Menampilkan halaman formulir tambah.
     */
    public function create(): View
    {
        $data = [
            'buildings' => Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']),
        ];
        return view('backend.master.floors.create', compact('data'));
    }

    /**
     * Menampilkan halaman detail (show).
     */
    public function showPage(string $id): View
    {
        $data = [
            'floor' => Floor::with(['building:id,name_building', 'creator:id,name', 'rooms'])->findOrFail($id)
        ];
        return view('backend.master.floors.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir edit.
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
     * Mengambil daftar lantai, bisa difilter berdasarkan building_id.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Floor::query()->orderBy('name_floor');

        // Filter berdasarkan building_id jika ada di request
        if ($request->has('building_id')) {
            $query->where('building_id', $request->input('building_id'));
        }

        return response()->json($query->get(['id', 'name_floor']));
    }

    /**
     * API: Menyimpan data lantai baru.
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name_floor' => 'required|string|max:50',
            'building_id' => 'required|exists:buildings,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $floor = Floor::create([
            'name_floor' => request('name_floor'),
            'building_id' => request('building_id'),
            'status' => request('status'),
            'created_by' => Auth::id(),
        ]);

        return response()->json($floor->load(['building:id,name_building', 'creator:id,name']), 201);
    }

    /**
     * API: Menampilkan satu data lantai spesifik.
     */
    public function show(string $id)
    {
        $floor = Floor::with(['building:id,name_building', 'creator:id,name'])->findOrFail($id);
        return response()->json($floor);
    }

    /**
     * API: Memperbarui data lantai yang sudah ada.
     */
    public function update(string $id)
    {
        $floor = Floor::findOrFail($id);

        $validator = Validator::make(request()->all(), [
            'name_floor' => 'required|string|max:50',
            'building_id' => 'required|exists:buildings,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $floor->update(request()->all());

        return response()->json($floor->load(['building:id,name_building', 'creator:id,name']));
    }

    /**
     * API: Menghapus data lantai.
     */
    public function destroy(string $id)
    {
        $floor = Floor::findOrFail($id);
        $floor->delete();

        return response()->json(null, 204);
    }
}
