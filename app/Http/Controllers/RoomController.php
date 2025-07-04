<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Building;
use App\Models\Floor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Method untuk menampilkan halaman Blade.
     */
    public function viewPage()
    {
        // Ambil data gedung dan lantai untuk dropdown di form
        $buildings = Building::where('status', 'active')->orderBy('name_building')->get(['id', 'name_building']);
        $floors = Floor::where('status', 'active')->orderBy('name_floor')->get(['id', 'name_floor', 'building_id']);

        return view('master.rooms.index', compact('buildings', 'floors'));
    }

    /**
     * Menampilkan daftar semua ruangan.
     */
    public function index()
    {
        // Eager load relasi floor, building, dan creator untuk efisiensi
        $rooms = Room::with(['floor.building', 'creator:id,name'])->latest()->get();
        return response()->json($rooms);
    }

    /**
     * Menyimpan data ruangan baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_room' => 'required|string|max:50',
            'floor_id' => 'required|exists:floors,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $room = Room::create([
            'name_room' => $request->name_room,
            'floor_id' => $request->floor_id,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return response()->json($room->load(['floor.building', 'creator:id,name']), 201);
    }

    /**
     * Menampilkan satu data ruangan spesifik.
     */
    public function show(string $id)
    {
        $room = Room::with(['floor.building', 'creator:id,name'])->findOrFail($id);
        return response()->json($room);
    }

    /**
     * Memperbarui data ruangan yang sudah ada.
     */
    public function update(Request $request, string $id)
    {
        $room = Room::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_room' => 'required|string|max:50',
            'floor_id' => 'required|exists:floors,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $room->update($request->all());

        return response()->json($room->load(['floor.building', 'creator:id,name']));
    }

    /**
     * Menghapus data ruangan.
     */
    public function destroy(string $id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return response()->json(null, 204);
    }
}
