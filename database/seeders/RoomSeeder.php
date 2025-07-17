<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::create(['floor_id' => 1, 'name_room' => 'Lobi Utama', 'status' => 'active']);
        Room::create(['floor_id' => 2, 'name_room' => 'Kantor Manager', 'status' => 'active']);
        Room::create(['floor_id' => 3, 'name_room' => 'Ruang Rapat Sakura', 'status' => 'active']);
        Room::create(['floor_id' => 3, 'name_room' => 'Gudang Lt. 5', 'status' => 'active']);
    }
}
