<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat kategori spesifik dengan kode singkatan
        AssetCategory::firstOrCreate(
            ['code' => 'AC'],
            ['name' => 'Air Conditioner']
        );
        AssetCategory::firstOrCreate(
            ['code' => 'MON'],
            ['name' => 'Monitor']
        );
        AssetCategory::firstOrCreate(
            ['code' => 'PRJ'],
            ['name' => 'Proyektor']
        );
        AssetCategory::firstOrCreate(
            ['code' => 'PRT'],
            ['name' => 'Printer']
        );
        AssetCategory::firstOrCreate(
            ['code' => 'LTP'],
            ['name' => 'Laptop']
        );
        AssetCategory::firstOrCreate(
            ['code' => 'CHR'],
            ['name' => 'Kursi Kerja']
        );
    }
}
