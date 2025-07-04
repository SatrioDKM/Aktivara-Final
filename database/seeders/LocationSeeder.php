<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Floor;
use App\Models\Building;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Gedung
        $gedungA = Building::create([
            'name_building' => 'Menara Sentosa',
            'address' => 'Jl. Jenderal Sudirman Kav. 52-53, Jakarta Selatan',
            'created_by' => 1, // Superadmin
        ]);

        // Buat Lantai di Gedung A
        $lantai1 = Floor::create(['building_id' => $gedungA->id, 'name_floor' => 'Lantai 1', 'created_by' => 1]);
        $lantai2 = Floor::create(['building_id' => $gedungA->id, 'name_floor' => 'Lantai 2', 'created_by' => 1]);
        $lantai5 = Floor::create(['building_id' => $gedungA->id, 'name_floor' => 'Lantai 5', 'created_by' => 1]);

        // Buat Ruangan di Lantai 1
        Room::create(['floor_id' => $lantai1->id, 'name_room' => 'Lobi Utama', 'created_by' => 1]);
        Room::create(['floor_id' => $lantai1->id, 'name_room' => 'Toilet Pria G-01', 'created_by' => 1]);
        Room::create(['floor_id' => $lantai1->id, 'name_room' => 'Gudang Pantry', 'created_by' => 1]);

        // Buat Ruangan di Lantai 2
        Room::create(['floor_id' => $lantai2->id, 'name_room' => 'Ruang Rapat Cendrawasih', 'created_by' => 1]);
        Room::create(['floor_id' => $lantai2->id, 'name_room' => 'Kantor Marketing', 'created_by' => 1]);

        // Buat Ruangan di Lantai 5
        Room::create(['floor_id' => $lantai5->id, 'name_room' => 'Ruang Server', 'created_by' => 1]);
        Room::create(['floor_id' => $lantai5->id, 'name_room' => 'Kantor Direksi', 'created_by' => 1]);
    }
}
