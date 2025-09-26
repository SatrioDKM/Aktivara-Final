<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Room;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Judul tugas yang realistis
        $taskTitles = [
            'Pengecekan rutin sistem hidran',
            'Pembersihan area lobi dan resepsionis',
            'Perbaikan lampu koridor lantai 2',
            'Patroli keamanan di area parkir B2',
            'Penggantian filter AC di ruang meeting',
            'Laporan insiden pintu darurat terbuka',
        ];

        return [
            // Kolom-kolom ini sebaiknya diisi secara spesifik di dalam Seeder
            // untuk menciptakan data yang bermakna dan terhubung.
            'task_type_id' => TaskType::inRandomOrder()->first()->id ?? null,
            'user_id' => null, // Diisi di seeder, null berarti belum ada staff yang mengerjakan
            'asset_id' => null, // Opsional, diisi di seeder jika terkait aset
            'room_id' => Room::inRandomOrder()->first()->id ?? null,
            'created_by' => User::where('role_id', 'like', '%01')->inRandomOrder()->first()->id ?? User::first()->id, // Diambil dari salah satu leader

            // Data yang di-generate oleh Faker
            'title' => $this->faker->randomElement($taskTitles),
            'description' => $this->faker->realText(150),
            'status' => 'unassigned', // Status default, bisa di-override di seeder
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'due_date' => now()->addDays(rand(1, 14)),
        ];
    }
}
