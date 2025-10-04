<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Floor;
use App\Models\Building;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Menampilkan halaman daftar ruangan (index).
     */
    public function viewPage(): View
    {
        $data = [
            'buildings' => Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']),
            'floors' => Floor::where('status', 'active')->orderBy('name_floor')->get(['id', 'name_floor', 'building_id']),
        ];
        return view('backend.master.rooms.index', compact('data'));
    }

    /**
     * Menampilkan halaman formulir tambah ruangan.
     */
    public function create(): View
    {
        $data = [
            'buildings' => Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']),
            'floors' => Floor::where('status', 'active')->orderBy('name_floor')->get(['id', 'name_floor', 'building_id']),
        ];
        return view('backend.master.rooms.create', compact('data'));
    }

    /**
     * Menampilkan halaman detail ruangan.
     */
    public function showPage(string $id): View
    {
        $data = [
            'room' => Room::with(['floor.building', 'creator:id,name'])->findOrFail($id)
        ];
        return view('backend.master.rooms.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir edit ruangan.
     */
    public function edit(string $id): View
    {
        $data = [
            'room' => Room::with('floor')->findOrFail($id),
            'buildings' => Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']),
            'floors' => Floor::where('status', 'active')->orderBy('name_floor')->get(['id', 'name_floor', 'building_id']),
        ];
        return view('backend.master.rooms.edit', compact('data'));
    }

    // ===================================================================
    // API METHODS
    // ===================================================================

    /**
     * API: Mengambil daftar ruangan dengan paginasi dan filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Room::with(['floor.building']);

        $query->when($request->input('search'), fn($q, $search) => $q->where('name_room', 'like', "%{$search}%"));
        $query->when($request->input('floor'), fn($q, $floorId) => $q->where('floor_id', $floorId));
        $query->when($request->input('building'), function ($q, $buildingId) {
            $q->whereHas('floor', fn($floorQuery) => $floorQuery->where('building_id', $buildingId));
        });

        $rooms = $query->latest()->paginate($request->input('perPage', 10));
        return response()->json($rooms);
    }

    /**
     * API: Menyimpan data ruangan baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name_room' => 'required|string|max:50',
            'floor_id' => 'required|exists:floors,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room = Room::create([
            'name_room' => $request->input('name_room'),
            'floor_id' => $request->input('floor_id'),
            'status' => $request->input('status'),
            'created_by' => Auth::id(),
        ]);

        return response()->json($room->load(['floor.building', 'creator:id,name']), 201);
    }

    /**
     * API: Menampilkan satu data ruangan spesifik.
     */
    public function show(string $id): JsonResponse
    {
        $room = Room::with(['floor.building', 'creator:id,name'])->findOrFail($id);
        return response()->json($room);
    }

    /**
     * API: Memperbarui data ruangan yang sudah ada.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $room = Room::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_room' => 'required|string|max:50',
            'floor_id' => 'required|exists:floors,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room->update($request->all());

        return response()->json($room->load(['floor.building', 'creator:id,name']));
    }

    /**
     * API: Menghapus data ruangan.
     */
    public function destroy(string $id): JsonResponse
    {
        $room = Room::findOrFail($id);
        $room->delete();
        return response()->json(['message' => 'Data ruangan berhasil dihapus.'], 200);
    }
}
