<?php

namespace Database\Seeders;

use App\Models\Floor;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Floor::create(['building_id' => 1, 'name_floor' => 'Lantai 1', 'status' => 'active']);
        Floor::create(['building_id' => 1, 'name_floor' => 'Lantai 2', 'status' => 'active']);
        Floor::create(['building_id' => 1, 'name_floor' => 'Lantai 5', 'status' => 'active']);
    }
}
