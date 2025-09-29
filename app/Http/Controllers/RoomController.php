<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * Menampilkan halaman utama (index).
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
     * Menampilkan halaman formulir tambah.
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
     * Menampilkan halaman detail (show).
     */
    public function showPage(string $id): View
    {
        $data = [
            'room' => Room::with(['floor.building', 'creator:id,name'])->findOrFail($id)
        ];
        return view('backend.master.rooms.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir edit.
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
     * API: Menampilkan daftar semua ruangan dengan paginasi dan filter.
     */
    public function index()
    {
        $query = Room::with(['floor.building', 'creator:id,name']);

        // Filter pencarian
        if (request('search', '')) {
            $query->where('name_room', 'like', '%' . request('search') . '%');
        }

        // Filter gedung
        if (request('building', '')) {
            $buildingId = request('building');
            $query->whereHas('floor', function ($q) use ($buildingId) {
                $q->where('building_id', $buildingId);
            });
        }

        // Filter lantai
        if (request('floor', '')) {
            $query->where('floor_id', request('floor'));
        }

        $rooms = $query->latest()->paginate(request('perPage', 10));

        return response()->json($rooms);
    }

    /**
     * API: Menyimpan data ruangan baru.
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name_room' => 'required|string|max:50',
            'floor_id' => 'required|exists:floors,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room = Room::create([
            'name_room' => request('name_room'),
            'floor_id' => request('floor_id'),
            'status' => request('status'),
            'created_by' => Auth::id(),
        ]);

        return response()->json($room->load(['floor.building', 'creator:id,name']), 201);
    }

    /**
     * API: Menampilkan satu data ruangan spesifik.
     */
    public function show(string $id)
    {
        $room = Room::with(['floor.building', 'creator:id,name'])->findOrFail($id);
        return response()->json($room);
    }

    /**
     * API: Memperbarui data ruangan yang sudah ada.
     */
    public function update(string $id)
    {
        $room = Room::findOrFail($id);

        $validator = Validator::make(request()->all(), [
            'name_room' => 'required|string|max:50',
            'floor_id' => 'required|exists:floors,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room->update(request()->all());

        return response()->json($room->load(['floor.building', 'creator:id,name']));
    }

    /**
     * API: Menghapus data ruangan.
     */
    public function destroy(string $id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return response()->json(null, 204);
    }
}
