<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Complaint;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user dan aset yang relevan untuk data contoh
        $manager = User::where('role_id', 'MG00')->first();
        $leaderHK = User::where('role_id', 'HK01')->first();
        $assetAC = Asset::where('serial_number', 'AC-2025-001')->first();

        if (!$manager || !$leaderHK) {
            // Jika user tidak ada, hentikan seeder
            $this->command->info('Tidak dapat menemukan user Manager atau Leader, ComplaintSeeder dilewati.');
            return;
        }

        // Contoh 1: Laporan yang masih terbuka
        Complaint::create([
            'title' => 'Toilet di Lobi Lt. 1 Mampet',
            'description' => 'Ada genangan air di toilet pria dekat lobi utama. Kemungkinan saluran pembuangan tersumbat. Mohon segera diperiksa.',
            'reporter_name' => 'Tamu Hotel (via Resepsionis)',
            'location_text' => 'Toilet Pria, Lobi Utama, Lantai 1',
            'status' => 'open',
            'room_id' => 1, // Pastikan ID ini ada
            'asset_id' => null,
            'created_by' => $leaderHK->id,
            'task_id' => null,
        ]);

        // Contoh 2: Laporan yang sudah dikonversi menjadi tugas
        if ($assetAC) {
            // Buat dulu tugasnya
            $relatedTask = Task::where('asset_id', $assetAC->id)->first();

            if ($relatedTask) {
                Complaint::create([
                    'title' => 'AC di Ruang Rapat Tidak Dingin',
                    'description' => 'Suhu AC di Ruang Rapat Sakura tidak bisa diatur dan terasa panas. Sudah dicoba restart tapi tidak berhasil.',
                    'reporter_name' => 'Staff IT',
                    'location_text' => 'Ruang Rapat Sakura, Lantai 5',
                    'status' => 'converted_to_task',
                    'room_id' => $assetAC->room_id,
                    'asset_id' => $assetAC->id,
                    'created_by' => $manager->id,
                    'task_id' => $relatedTask->id, // Tautkan ke tugas yang sudah ada
                ]);
            }
        }
    }
}
