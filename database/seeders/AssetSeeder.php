<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID user admin untuk kolom created_by/updated_by
        $adminUser = User::where('role_id', 'SA00')->first();

        // Data untuk Aset Tetap (Fixed Assets)
        $fixedAssets = [
            [
                'name_asset' => 'AC Split 2PK',
                'asset_type' => 'fixed_asset',
                'category' => 'Elektronik Pendingin',
                'serial_number' => 'AC-2025-001',
                'condition' => 'Baik',
                'current_stock' => 1,
                'minimum_stock' => 0, // Aset tetap tidak punya stok minimum
                'status' => 'available',
                'description' => 'AC di Ruang Rapat Lt. 5',
                'room_id' => 1, // Pastikan ID ini ada di tabel rooms
            ],
            [
                'name_asset' => 'Proyektor InFocus X1',
                'asset_type' => 'fixed_asset',
                'category' => 'Elektronik Presentasi',
                'serial_number' => 'PROJ-2025-001',
                'condition' => 'Baik',
                'current_stock' => 1,
                'minimum_stock' => 0,
                'status' => 'available',
                'description' => 'Proyektor portable untuk meeting.',
                'room_id' => null, // Disimpan di gudang
            ],
            [
                'name_asset' => 'Meja Kerja Kayu',
                'asset_type' => 'fixed_asset',
                'category' => 'Furniture Kantor',
                'serial_number' => 'FURN-2025-010',
                'condition' => 'Baik',
                'current_stock' => 1,
                'minimum_stock' => 0,
                'status' => 'in_use',
                'description' => 'Meja di ruang kerja Manager.',
                'room_id' => 2, // Pastikan ID ini ada di tabel rooms
            ],
        ];

        // Data untuk Barang Habis Pakai (Consumables)
        $consumableAssets = [
            [
                'name_asset' => 'Spidol Papan Tulis (Hitam)',
                'asset_type' => 'consumable',
                'category' => 'Alat Tulis Kantor',
                'current_stock' => 50,
                'minimum_stock' => 10, // Ada stok minimum
                'status' => 'available',
                'description' => 'Spidol merek Snowman warna hitam.',
                'room_id' => null, // Disimpan di gudang
            ],
            [
                'name_asset' => 'Cairan Pembersih Lantai (Lemon)',
                'asset_type' => 'consumable',
                'category' => 'Peralatan Kebersihan',
                'current_stock' => 20,
                'minimum_stock' => 5,
                'status' => 'available',
                'description' => 'Super Pell 1 Liter aroma Lemon.',
                'room_id' => null,
            ],
            [
                'name_asset' => 'Bohlam LED 12W',
                'asset_type' => 'consumable',
                'category' => 'Kelistrikan',
                'current_stock' => 30,
                'minimum_stock' => 10,
                'status' => 'available',
                'description' => 'Bohlam LED Philips 12 Watt warna putih.',
                'room_id' => null,
            ],
        ];

        // Gabungkan semua data aset
        $allAssets = array_merge($fixedAssets, $consumableAssets);

        foreach ($allAssets as $assetData) {
            Asset::create(array_merge($assetData, [
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id
            ]));
        }
    }
}
