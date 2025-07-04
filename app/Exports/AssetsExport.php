<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AssetsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * Mengambil data aset dari database menggunakan query.
     * Eager loading digunakan untuk efisiensi.
     */
    public function query()
    {
        return Asset::query()->with(['room.floor.building', 'updater']);
    }

    /**
     * Mendefinisikan judul untuk setiap kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'ID Aset',
            'Nama Aset',
            'Nomor Seri',
            'Kategori',
            'Lokasi (Ruangan)',
            'Lokasi (Lantai)',
            'Lokasi (Gedung)',
            'Kondisi',
            'Status',
            'Stok Saat Ini',
            'Stok Minimum',
            'Tanggal Pembelian',
            'Terakhir Diperbarui Oleh',
            'Tanggal Dibuat',
        ];
    }

    /**
     * Memetakan data dari setiap model Asset ke dalam format baris Excel.
     * @param \App\Models\Asset $asset
     */
    public function map($asset): array
    {
        return [
            $asset->id,
            $asset->name_asset,
            $asset->serial_number,
            $asset->category,
            $asset->room->name_room ?? 'Gudang',
            $asset->room->floor->name_floor ?? '-',
            $asset->room->floor->building->name_building ?? '-',
            $asset->condition,
            $asset->status,
            $asset->current_stock,
            $asset->minimum_stock,
            $asset->purchase_date,
            $asset->updater->name ?? 'N/A',
            $asset->created_at->format('d-m-Y H:i:s'),
        ];
    }
}
