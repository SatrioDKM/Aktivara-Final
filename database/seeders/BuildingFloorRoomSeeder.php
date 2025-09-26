<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class BuildingFloorRoomSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil satu user untuk data 'created_by'
        $manager = User::where('role_id', 'MG00')->first();
        if (!$manager) {
            $this->command->info('Manager user not found, skipping BuildingFloorRoomSeeder.');
            return;
        }

        // 1. Buat Gedung
        $building = Building::create([
            'name_building' => 'Gedung Utama Manpro',
            'address' => 'Jl. Teknologi No. 1, Jakarta',
            'created_by' => $manager->id,
            'status' => 'active',
        ]);

        // 2. Buat Lantai di dalam Gedung tersebut
        $floor1 = Floor::create(['building_id' => $building->id, 'name_floor' => 'Lantai 1', 'created_by' => $manager->id]);
        $floor2 = Floor::create(['building_id' => $building->id, 'name_floor' => 'Lantai 2', 'created_by' => $manager->id]);
        $floor5 = Floor::create(['building_id' => $building->id, 'name_floor' => 'Lantai 5 (Rooftop)', 'created_by' => $manager->id]);

        // 3. Buat Ruangan di setiap Lantai
        $rooms = [
            // Lantai 1
            ['floor_id' => $floor1->id, 'name_room' => 'Lobi Utama', 'created_by' => $manager->id],
            ['floor_id' => $floor1->id, 'name_room' => 'Toilet Pria Lobi', 'created_by' => $manager->id],
            ['floor_id' => $floor1->id, 'name_room' => 'Toilet Wanita Lobi', 'created_by' => $manager->id],
            ['floor_id' => $floor1->id, 'name_room' => 'Gudang Parkir B1', 'created_by' => $manager->id],
            // Lantai 2
            ['floor_id' => $floor2->id, 'name_room' => 'Kantor Manajer', 'created_by' => $manager->id],
            ['floor_id' => $floor2->id, 'name_room' => 'Ruang Server', 'created_by' => $manager->id],
            ['floor_id' => $floor2->id, 'name_room' => 'Pantry Lt. 2', 'created_by' => $manager->id],
            // Lantai 5
            ['floor_id' => $floor5->id, 'name_room' => 'Ruang Rapat Sakura', 'created_by' => $manager->id],
            ['floor_id' => $floor5->id, 'name_room' => 'Taman Rooftop', 'created_by' => $manager->id],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
