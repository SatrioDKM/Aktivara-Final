<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetCategory; // <-- DITAMBAHKAN
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('role_id', 'SA00')->first();
        $officeRoom = Room::where('name_room', 'Kantor Manajer')->first();
        $meetingRoom = Room::where('name_room', 'Ruang Rapat Sakura')->first();
        $storageRoom = Room::where('name_room', 'Gudang Parkir B1')->first();

        if (!$adminUser || !$officeRoom || !$meetingRoom || !$storageRoom) {
            $this->command->info('Prerequisite User/Room not found, skipping AssetSeeder.');
            return;
        }

        // --- PERBAIKAN: Buat atau cari Kategori Aset ---
        $catElektronik = AssetCategory::firstOrCreate(['name' => 'Elektronik']);
        $catFurnitur = AssetCategory::firstOrCreate(['name' => 'Furnitur']);
        $catKebersihan = AssetCategory::firstOrCreate(['name' => 'Kebersihan']);
        $catKonsumsi = AssetCategory::firstOrCreate(['name' => 'Konsumsi']);
        $catKelistrikan = AssetCategory::firstOrCreate(['name' => 'Kelistrikan']);
        $catATK = AssetCategory::firstOrCreate(['name' => 'ATK']);
        // --- AKHIR PERBAIKAN ---

        // Kebutuhan: Aset Tetap (5-10 data)
        $fixedAssets = [
            [
                'name_asset' => 'AC Central Daikin',
                'asset_category_id' => $catElektronik->id, // <-- PERBAIKAN
                'serial_number' => 'AC-2025-001',
                'condition' => 'Baik',
                'status' => 'in_use',
                'room_id' => $meetingRoom->id
            ],
            [
                'name_asset' => 'Proyektor Epson EB-X500',
                'asset_category_id' => $catElektronik->id, // <-- PERBAIKAN
                'serial_number' => 'PROJ-2025-001',
                'condition' => 'Baik',
                'status' => 'available',
                'room_id' => $meetingRoom->id
            ],
            [
                'name_asset' => 'Meja Kerja Manajer',
                'asset_category_id' => $catFurnitur->id, // <-- PERBAIKAN
                'serial_number' => 'MEJA-2025-001',
                'condition' => 'Baik',
                'status' => 'in_use',
                'room_id' => $officeRoom->id
            ],
        ];

        foreach ($fixedAssets as $asset) {
            Asset::create(array_merge($asset, [
                'asset_type' => 'fixed_asset',
                'created_by' => $adminUser->id,
                'current_stock' => 1, // Aset tetap biasanya 1
                'minimum_stock' => 1,
            ]));
        }

        // Kebutuhan: Aset Habis Pakai (Stok Normal & Stok Menipis)
        $consumableAssets = [
            // Stok Normal
            [
                'name_asset' => 'Kopi Sachet ABC',
                'asset_category_id' => $catKonsumsi->id, // <-- PERBAIKAN
                'current_stock' => 15,
                'minimum_stock' => 5,
                'status' => 'available'
            ],
            // Stok Menipis
            [
                'name_asset' => 'Bohlam LED Philips 12W',
                'asset_category_id' => $catKelistrikan->id, // <-- PERBAIKAN
                'current_stock' => 8,
                'minimum_stock' => 10,
                'status' => 'available'
            ],
            [
                'name_asset' => 'Cairan Pembersih Lantai 1L',
                'asset_category_id' => $catKebersihan->id, // <-- PERBAIKAN
                'current_stock' => 4,
                'minimum_stock' => 5,
                'status' => 'available'
            ],
            [
                'name_asset' => 'Baterai AA Alkaline',
                'asset_category_id' => $catElektronik->id, // <-- PERBAIKAN
                'current_stock' => 20,
                'minimum_stock' => 24,
                'status' => 'available'
            ],
            [
                'name_asset' => 'Spidol Papan Tulis Hitam',
                'asset_category_id' => $catATK->id, // <-- PERBAIKAN
                'current_stock' => 10,
                'minimum_stock' => 12,
                'status' => 'available'
            ],
        ];

        foreach ($consumableAssets as $asset) {
            Asset::create(array_merge($asset, [
                'asset_type' => 'consumable',
                'room_id' => $storageRoom->id,
                'created_by' => $adminUser->id,
                'condition' => 'Baik',
            ]));
        }
    }
}
