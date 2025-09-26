<?php

namespace Database\Seeders;

use App\Models\Asset;
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

        // Kebutuhan: Aset Tetap (5-10 data)
        $fixedAssets = [
            ['name_asset' => 'AC Central Daikin', 'category' => 'Elektronik', 'serial_number' => 'AC-2025-001', 'condition' => 'Baik', 'status' => 'in_use', 'room_id' => $meetingRoom->id],
            ['name_asset' => 'Proyektor Epson EB-X500', 'category' => 'Elektronik', 'serial_number' => 'PROJ-2025-001', 'condition' => 'Baik', 'status' => 'available', 'room_id' => $storageRoom->id],
            ['name_asset' => 'Meja Direksi Jati', 'category' => 'Furniture', 'serial_number' => 'FURN-2025-001', 'condition' => 'Baik', 'status' => 'in_use', 'room_id' => $officeRoom->id],
            ['name_asset' => 'CCTV Hikvision Outdoor', 'category' => 'Keamanan', 'serial_number' => 'CCTV-2025-001', 'condition' => 'Perlu Perbaikan', 'status' => 'available', 'room_id' => $storageRoom->id],
            ['name_asset' => 'Genset Perkins 100 kVA', 'category' => 'Mekanikal', 'serial_number' => 'GEN-2025-001', 'condition' => 'Baik', 'status' => 'in_use', 'room_id' => $storageRoom->id],
        ];

        foreach ($fixedAssets as $asset) {
            Asset::create(array_merge($asset, [
                'asset_type' => 'fixed_asset',
                'current_stock' => 1,
                'minimum_stock' => 0,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ]));
        }

        // Kebutuhan: Barang Habis Pakai & Barang Stok Menipis (total 5-10 data)
        $consumableAssets = [
            // Stok Normal
            ['name_asset' => 'Kertas A4 70gr Rim', 'category' => 'ATK', 'current_stock' => 50, 'minimum_stock' => 10, 'status' => 'available'],
            ['name_asset' => 'Tinta Printer HP 682 Hitam', 'category' => 'ATK', 'current_stock' => 25, 'minimum_stock' => 5, 'status' => 'available'],
            ['name_asset' => 'Galon Air Mineral', 'category' => 'Konsumsi', 'current_stock' => 15, 'minimum_stock' => 5, 'status' => 'available'],
            // Stok Menipis
            ['name_asset' => 'Bohlam LED Philips 12W', 'category' => 'Kelistrikan', 'current_stock' => 8, 'minimum_stock' => 10, 'status' => 'available'],
            ['name_asset' => 'Cairan Pembersih Lantai 1L', 'category' => 'Kebersihan', 'current_stock' => 4, 'minimum_stock' => 5, 'status' => 'available'],
            ['name_asset' => 'Baterai AA Alkaline', 'category' => 'Elektronik', 'current_stock' => 20, 'minimum_stock' => 24, 'status' => 'available'],
            ['name_asset' => 'Spidol Papan Tulis Hitam', 'category' => 'ATK', 'current_stock' => 10, 'minimum_stock' => 12, 'status' => 'available'],
        ];

        foreach ($consumableAssets as $asset) {
            Asset::create(array_merge($asset, [
                'asset_type' => 'consumable',
                'room_id' => $storageRoom->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ]));
        }
    }
}
