<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetsMaintenance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssetsMaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the specific asset and the technician
        $asset = Asset::where('serial_number', 'AC-2025-001')->first();
        $technician = User::where('role_id', 'TK02')->first();

        // Only create the seeder if both the asset and technician are found
        if ($asset && $technician) {
            AssetsMaintenance::create([
                'asset_id' => $asset->id,
                'user_id' => $technician->id, // <-- RENAMED from 'technician_id'
                'description_text' => 'Pembersihan filter dan pengecekan freon rutin.',
                'status' => 'completed',
                'notes' => 'Tidak ada masalah ditemukan, AC berfungsi normal.',
            ]);
        }
    }
}
