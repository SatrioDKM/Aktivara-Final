<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\TaskTypeSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Master data yang tidak memiliki dependensi
            RoleSeeder::class,

            // 2. User bergantung pada Roles
            UserSeeder::class,

            // 3. Lokasi bergantung pada User (created_by)
            LocationSeeder::class,

            // 4. Master data lainnya
            TaskTypeSeeder::class,

            // 5. Aset bergantung pada Lokasi (Rooms) dan User
            AssetSeeder::class,
        ]);
    }
}
