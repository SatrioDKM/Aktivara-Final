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
        TaskType::create([
            'name_task' => 'Pembersihan Toilet',
            'departemen' => 'HK',
            'priority_level' => 'medium',
            'description' => 'Membersihkan seluruh area toilet termasuk wastafel, kloset, dan lantai.'
        ]);

        TaskType::create([
            'name_task' => 'Perbaikan AC',
            'departemen' => 'TK',
            'priority_level' => 'high',
            'description' => 'Memeriksa dan memperbaiki unit AC yang dilaporkan tidak dingin.'
        ]);

        TaskType::create([
            'name_task' => 'Patroli Keamanan',
            'departemen' => 'SC',
            'priority_level' => 'medium',
            'description' => 'Melakukan patroli rutin di area yang telah ditentukan.'
        ]);

        TaskType::create([
            'name_task' => 'Penggantian Lampu',
            'departemen' => 'TK',
            'priority_level' => 'low',
            'description' => 'Mengganti bola lampu yang mati atau rusak.'
        ]);
    }
}
