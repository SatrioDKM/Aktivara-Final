<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Building::create([
            'name_building' => 'Gedung Utama',
            'address' => 'Jl. Merdeka No. 10, Jakarta',
            'status' => 'active',
        ]);
    }
}
