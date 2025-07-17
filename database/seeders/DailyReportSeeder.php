<?php

namespace Database\Seeders;

use App\Models\DailyReport;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DailyReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the relevant Staff and Leader
        $staff = User::where('role_id', 'SC02')->first();
        $leader = User::where('role_id', 'SC01')->first();

        // Only create the seeder if the users are found
        if ($staff && $leader) {
            DailyReport::create([
                'user_id' => $staff->id,
                'title' => 'Laporan Jaga Malam Shift B',
                'description' => 'Patroli shift malam berjalan lancar. Semua area terpantau aman dan kondusif. Tidak ada laporan insiden khusus.',
                'status' => 'submitted',
                // The 'reviewed_by' field can be populated if you want to seed a completed report
                'reviewed_by' => null,
            ]);
        }
    }
}
