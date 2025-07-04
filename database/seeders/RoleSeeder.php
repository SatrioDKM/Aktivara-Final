<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['role_id' => 'SA00', 'role_name' => 'Superadmin'],
            ['role_id' => 'MG00', 'role_name' => 'Manager'],
            ['role_id' => 'HK01', 'role_name' => 'Leader Housekeeping'],
            ['role_id' => 'HK02', 'role_name' => 'Staff Housekeeping'],
            ['role_id' => 'TK01', 'role_name' => 'Leader Teknisi'],
            ['role_id' => 'TK02', 'role_name' => 'Staff Teknisi'],
            ['role_id' => 'SC01', 'role_name' => 'Leader Security'],
            ['role_id' => 'SC02', 'role_name' => 'Staff Security'],
        ]);
    }
}
