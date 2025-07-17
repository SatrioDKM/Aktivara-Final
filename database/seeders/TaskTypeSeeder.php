<?php

namespace Database\Seeders;

use App\Models\TaskType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taskTypes = [
            // Housekeeping
            ['departemen' => 'HK', 'name_task' => 'Pembersihan Rutin'],
            ['departemen' => 'HK', 'name_task' => 'Pengelolaan Sampah'],

            // Teknisi
            ['departemen' => 'TK', 'name_task' => 'Perbaikan Kelistrikan'],
            ['departemen' => 'TK', 'name_task' => 'Perbaikan Aset'],
            ['departemen' => 'TK', 'name_task' => 'Instalasi Baru'],

            // Security
            ['departemen' => 'SC', 'name_task' => 'Patroli Keamanan'],
            ['departemen' => 'SC', 'name_task' => 'Pengawalan Tamu VIP'],

            // Parking
            ['departemen' => 'PK', 'name_task' => 'Pengaturan Lalu Lintas'],

            // Umum (bisa diakses semua departemen)
            ['departemen' => 'UMUM', 'name_task' => 'Laporan Insiden'],
            ['departemen' => 'UMUM', 'name_task' => 'Permintaan Bantuan'],
        ];

        foreach ($taskTypes as $type) {
            TaskType::create($type);
        }
    }
}
