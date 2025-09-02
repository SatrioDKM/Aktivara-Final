<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\RoomSeeder;
use Database\Seeders\TaskSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\FloorSeeder;
use Database\Seeders\BuildingSeeder;
use Database\Seeders\TaskTypeSeeder;
use Database\Seeders\ComplaintSeeder;
use Database\Seeders\DailyReportSeeder;
use Database\Seeders\AssetsMaintenanceSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            BuildingSeeder::class,
            FloorSeeder::class,
            RoomSeeder::class,
            TaskTypeSeeder::class,
            AssetSeeder::class,
            TaskSeeder::class,
            AssetsMaintenanceSeeder::class,
            DailyReportSeeder::class,
            ComplaintSeeder::class,
        ]);
    }
}
