<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TaskSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\TaskTypeSeeder;
use Database\Seeders\ComplaintSeeder;
use Database\Seeders\PackingListSeeder;
use Database\Seeders\BuildingFloorRoomSeeder;
use Database\Seeders\AssetsInMaintenanceSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Seeders dasar yang sudah ada
            RoleSeeder::class,
            UserSeeder::class,
            TaskTypeSeeder::class,

            // Seeder baru untuk data master & transaksional
            BuildingFloorRoomSeeder::class, // Wajib sebelum Aset & Tugas
            AssetSeeder::class,             // Wajib sebelum seeder lain yg butuh aset
            TaskSeeder::class,              // Wajib sebelum Laporan
            AssetsInMaintenanceSeeder::class, // Mengubah status aset yg sudah ada
            PackingListSeeder::class,         // Untuk pergerakan barang keluar
            ComplaintSeeder::class,
        ]);
    }
}
