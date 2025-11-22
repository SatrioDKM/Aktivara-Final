<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Data Pendukung (User & Room)
        $adminUser = User::where('role_id', 'SA00')->first();
        $officeRoom = Room::where('name_room', 'Kantor Manajer')->first();
        $meetingRoom = Room::where('name_room', 'Ruang Rapat Sakura')->first();
        $storageRoom = Room::where('name_room', 'Gudang Parkir B1')->first();

        // Cek jika data pendukung tidak ada
        if (!$adminUser || !$officeRoom || !$meetingRoom || !$storageRoom) {
            $this->command->info('User admin atau Ruangan belum dibuat. Pastikan UserSeeder & RoomSeeder sudah jalan.');
            return;
        }

        // 2. Buat Kategori Spesifik (Sesuai Request: AC, Monitor, dll)
        // Format: firstOrCreate(['code' => 'KODE'], ['name' => 'NAMA'])
        $catAC  = AssetCategory::firstOrCreate(['code' => 'AC'], ['name' => 'Air Conditioner']);
        $catMON = AssetCategory::firstOrCreate(['code' => 'MON'], ['name' => 'Monitor']);
        $catPRJ = AssetCategory::firstOrCreate(['code' => 'PRJ'], ['name' => 'Proyektor']);
        $catLTP = AssetCategory::firstOrCreate(['code' => 'LTP'], ['name' => 'Laptop']);
        $catFUR = AssetCategory::firstOrCreate(['code' => 'FUR'], ['name' => 'Furniture']); // Untuk Meja/Kursi
        
        // Kategori untuk Barang Habis Pakai (Biar logis)
        $catFNB = AssetCategory::firstOrCreate(['code' => 'FNB'], ['name' => 'Konsumsi']);
        $catCLN = AssetCategory::firstOrCreate(['code' => 'CLN'], ['name' => 'Kebersihan']);
        $catATK = AssetCategory::firstOrCreate(['code' => 'ATK'], ['name' => 'Alat Tulis Kantor']);
        $catELK = AssetCategory::firstOrCreate(['code' => 'ELK'], ['name' => 'Elektronik Umum']);

        // 3. Fungsi Generator Serial Number (Logic Lokal untuk Seeder)
        $generateSN = function ($categoryCode) {
            static $counters = [];
            
            if (!isset($counters[$categoryCode])) {
                $counters[$categoryCode] = 1;
            } else {
                $counters[$categoryCode]++;
            }

            // Format: KODE-YYYYMMDD-000X (Sesuai standar baru)
            return $categoryCode . '-' . date('Ymd') . '-' . str_pad($counters[$categoryCode], 4, '0', STR_PAD_LEFT);
        };

        // 4. Data Aset Tetap (Fixed Assets)
        $fixedAssets = [
            [
                'name_asset' => 'AC Central Daikin',
                'cat_obj' => $catAC, // Pakai Object agar bisa ambil ID dan CODE
                'condition' => 'Baik',
                'status' => 'in_use',
                'room_id' => $meetingRoom->id
            ],
            [
                'name_asset' => 'Proyektor Epson EB-X500',
                'cat_obj' => $catPRJ,
                'condition' => 'Baik',
                'status' => 'available',
                'room_id' => $meetingRoom->id
            ],
            [
                'name_asset' => 'Monitor LG 24 Inch',
                'cat_obj' => $catMON,
                'condition' => 'Baik',
                'status' => 'in_use',
                'room_id' => $officeRoom->id
            ],
            [
                'name_asset' => 'Meja Kerja Manajer',
                'cat_obj' => $catFUR,
                'condition' => 'Baik',
                'status' => 'in_use',
                'room_id' => $officeRoom->id
            ],
        ];

        foreach ($fixedAssets as $item) {
            Asset::create([
                'name_asset' => $item['name_asset'],
                'asset_category_id' => $item['cat_obj']->id,
                'asset_type' => 'fixed_asset',
                'serial_number' => $generateSN($item['cat_obj']->code), // Generate SN di sini!
                'condition' => $item['condition'],
                'status' => $item['status'],
                'room_id' => $item['room_id'],
                'current_stock' => 1,
                'minimum_stock' => 1,
                'created_by' => $adminUser->id,
            ]);
        }

        // 5. Data Aset Habis Pakai (Consumables)
        // Kategori disesuaikan agar logis
        $consumableAssets = [
            [
                'name' => 'Kopi Sachet ABC', 
                'cat_obj' => $catFNB, 
                'stock' => 15, 
                'min' => 5
            ],
            [
                'name' => 'Bohlam LED Philips 12W', 
                'cat_obj' => $catELK, 
                'stock' => 8, 
                'min' => 10
            ],
            [
                'name' => 'Cairan Pembersih Lantai 1L', 
                'cat_obj' => $catCLN, 
                'stock' => 4, 
                'min' => 5
            ],
            [
                'name' => 'Baterai AA Alkaline', 
                'cat_obj' => $catELK, 
                'stock' => 20, 
                'min' => 24
            ],
            [
                'name' => 'Spidol Papan Tulis Hitam', 
                'cat_obj' => $catATK, 
                'stock' => 10, 
                'min' => 12
            ],
        ];

        foreach ($consumableAssets as $item) {
            Asset::create([
                'name_asset' => $item['name'],
                'asset_category_id' => $item['cat_obj']->id,
                'asset_type' => 'consumable',
                'serial_number' => $generateSN($item['cat_obj']->code), // Tetap generate SN biar rapi
                'condition' => 'Baik',
                'status' => 'available',
                'room_id' => $storageRoom->id,
                'current_stock' => $item['stock'],
                'minimum_stock' => $item['min'],
                'created_by' => $adminUser->id,
            ]);
        }
    }
}