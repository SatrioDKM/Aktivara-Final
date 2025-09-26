<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Room;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil User
        $leaderTeknisi = User::where('role_id', 'TK01')->first();
        $staffTeknisi1 = User::where('email', 'staff.tk@example.com')->first();
        $leaderHK = User::where('role_id', 'HK01')->first();
        $staffHK1 = User::where('email', 'staff.hk@example.com')->first();

        // Ambil Aset & Ruangan
        $ac = Asset::where('serial_number', 'AC-2025-001')->first();
        $lobbyToilet = Room::where('name_room', 'Toilet Pria Lobi')->first();

        // Ambil Jenis Tugas
        $taskTypeRepair = TaskType::where('name_task', 'Perbaikan Aset')->first();
        $taskTypeCleaning = TaskType::where('name_task', 'Pembersihan Rutin')->first();

        if (!$leaderTeknisi || !$staffTeknisi1 || !$leaderHK || !$staffHK1 || !$ac || !$lobbyToilet || !$taskTypeRepair || !$taskTypeCleaning) {
            $this->command->info('Prerequisite data not found, skipping TaskSeeder.');
            return;
        }

        // Kebutuhan: Tugas Baru (Unassigned) (5 data)
        Task::factory(5)->create([
            'status' => 'unassigned',
            'created_by' => $leaderHK->id,
            'user_id' => null, // Belum ada staff yang ambil
            'task_type_id' => $taskTypeCleaning->id,
            'room_id' => $lobbyToilet->id,
            'priority' => 'low',
        ]);

        // Kebutuhan: Tugas Dikerjakan (In Progress) (5 data)
        Task::factory(5)->create([
            'status' => 'in_progress',
            'created_by' => $leaderTeknisi->id,
            'user_id' => $staffTeknisi1->id, // Sudah dikerjakan staff
            'task_type_id' => $taskTypeRepair->id,
            'asset_id' => $ac->id,
            'priority' => 'medium',
        ]);

        // Kebutuhan: Menunggu Review (Pending Review) (5 data)
        Task::factory(5)->create([
            'status' => 'pending_review',
            'created_by' => $leaderHK->id,
            'user_id' => $staffHK1->id,
            'task_type_id' => $taskTypeCleaning->id,
            'room_id' => $lobbyToilet->id,
            'priority' => 'high',
        ]);
    }
}
