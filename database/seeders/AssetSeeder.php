<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Aset di Ruang Server (Room ID 6)
        Asset::create([
            'room_id' => 6,
            'name_asset' => 'AC Split 2 PK Daikin',
            'category' => 'Elektronik',
            'serial_number' => 'DKN-SERV-001',
            'purchase_date' => '2022-01-15',
            'condition' => 'Baik',
            'status' => 'in_use',
            'current_stock' => 1,
            'minimum_stock' => 0,
            'created_by' => 1, // <-- Tambahkan ini
            'updated_by' => 1,
        ]);

        // Aset di Gudang Pantry (Room ID 3)
        Asset::create([
            'room_id' => 3,
            'name_asset' => 'Cairan Pembersih Lantai (L)',
            'category' => 'Logistik',
            'status' => 'available',
            'current_stock' => 20,
            'minimum_stock' => 5,
            'created_by' => 1, // <-- Tambahkan ini
            'updated_by' => 1,
        ]);

        Asset::create([
            'room_id' => 3,
            'name_asset' => 'Bohlam LED 12 Watt',
            'category' => 'Elektronik',
            'status' => 'available',
            'current_stock' => 50,
            'minimum_stock' => 10,
            'created_by' => 1, // <-- Tambahkan ini
            'updated_by' => 1,
        ]);
    }
}
