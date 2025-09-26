<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetsMaintenance;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssetsInMaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $technician = User::where('role_id', 'TK02')->first();
        // Ambil aset yang BUKAN dalam perbaikan untuk dijadikan data contoh
        $assetsToMaintain = Asset::where('status', '!=', 'maintenance')
            ->where('asset_type', 'fixed_asset')
            ->take(5)->get();

        if (!$technician || $assetsToMaintain->isEmpty()) {
            $this->command->info('Technician or available assets not found, skipping AssetsInMaintenanceSeeder.');
            return;
        }

        foreach ($assetsToMaintain as $asset) {
            // 1. Buat record maintenance
            AssetsMaintenance::create([
                'asset_id' => $asset->id,
                'user_id' => $technician->id,
                'start_date' => now(),
                'maintenance_type' => 'repair',
                'description' => 'Aset sedang dalam proses perbaikan terjadwal.',
                'status' => 'in_progress',
            ]);

            // 2. Update status aset menjadi 'maintenance'
            $asset->update(['status' => 'maintenance']);
        }
    }
}
