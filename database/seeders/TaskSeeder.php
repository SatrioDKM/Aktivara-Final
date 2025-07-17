<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data user yang relevan
        $leaderTeknisi = User::where('role_id', 'TK01')->first();
        $staffTeknisi = User::where('role_id', 'TK02')->first();
        $leaderHK = User::where('role_id', 'HK01')->first();
        $staffHK = User::where('role_id', 'HK02')->first();

        // Ambil data aset yang relevan
        $ac = Asset::where('serial_number', 'AC-2025-001')->first();
        $bohlam = Asset::where('name_asset', 'Bohlam LED 12W')->first();

        // Data tugas
        $tasks = [
            // Tugas Maintenance AC (terkait dengan Aset Tetap)
            [
                'title' => 'Perbaikan AC di Ruang Rapat',
                'task_type_id' => 4, // ID untuk jenis tugas 'Perbaikan Aset'
                'asset_id' => $ac->id,
                'room_id' => $ac->room_id,
                'priority' => 'high',
                'description' => 'AC tidak dingin, tolong segera diperiksa dan diperbaiki.',
                'status' => 'completed',
                'created_by' => $leaderTeknisi->id,
                'user_id' => $staffTeknisi->id, // Sudah dikerjakan staff teknisi
                // Kolom 'report_text', 'report_image', 'reviewed_by', 'review_notes' DIHAPUS DARI SINI
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(4),
            ],
            // Tugas Ganti Bohlam (terkait dengan Barang Habis Pakai)
            [
                'title' => 'Ganti Bohlam di Lobi Lt. 1',
                'task_type_id' => 5, // ID untuk jenis tugas 'Instalasi'
                'asset_id' => $bohlam->id,
                'room_id' => 3, // Pastikan ID ini ada
                'priority' => 'medium',
                'description' => 'Bohlam di lobi mati, tolong diganti dengan yang baru dari stok.',
                'status' => 'completed',
                'created_by' => $leaderTeknisi->id,
                'user_id' => $staffTeknisi->id,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
            ],
            // Tugas Kebersihan (tidak terkait aset)
            [
                'title' => 'Pembersihan Rutin Area Parkir B1',
                'task_type_id' => 1, // ID untuk jenis tugas 'Kebersihan'
                'asset_id' => null,
                'room_id' => null,
                'priority' => 'low',
                'description' => 'Lakukan pembersihan harian untuk area parkir basement 1.',
                'status' => 'in_progress',
                'created_by' => $leaderHK->id,
                'user_id' => $staffHK->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }
    }
}
