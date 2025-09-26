<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\PackingList;
use App\Models\User;
use Illuminate\Database\Seeder;

class PackingListSeeder extends Seeder
{
    public function run(): void
    {
        $warehouseLeader = User::where('role_id', 'WH01')->first();
        $assetsToPack = Asset::where('asset_type', 'consumable')->take(10)->get();

        if (!$warehouseLeader || $assetsToPack->count() < 5) {
            $this->command->info('Warehouse leader or assets not found, skipping PackingListSeeder.');
            return;
        }

        // Buat 2 Packing List berbeda
        $packingList1 = PackingList::create([
            'document_number' => 'PL-2025-001',
            'recipient_name' => 'Departemen Housekeeping',
            'created_by' => $warehouseLeader->id,
            'notes' => 'Pengambilan stok bulanan.',
        ]);

        $packingList2 = PackingList::create([
            'document_number' => 'PL-2025-002',
            'recipient_name' => 'Departemen Teknisi',
            'created_by' => $warehouseLeader->id,
            'notes' => 'Kebutuhan perbaikan di lantai 5.',
        ]);

        // Lampirkan beberapa aset ke setiap packing list (many-to-many)
        $packingList1->assets()->attach(
            $assetsToPack->slice(0, 3)->pluck('id')->toArray()
        );

        $packingList2->assets()->attach(
            $assetsToPack->slice(3, 2)->pluck('id')->toArray()
        );
    }
}
