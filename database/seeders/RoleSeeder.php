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
            // Peran Administratif
            ['role_id' => 'SA00', 'role_name' => 'Superadmin'],
            ['role_id' => 'MG00', 'role_name' => 'Manager'],

            // Peran Housekeeping
            ['role_id' => 'HK01', 'role_name' => 'Leader Housekeeping'],
            ['role_id' => 'HK02', 'role_name' => 'Staff Housekeeping'],

            // Peran Teknisi
            ['role_id' => 'TK01', 'role_name' => 'Leader Teknisi'],
            ['role_id' => 'TK02', 'role_name' => 'Staff Teknisi'],

            // Peran Parkir
            ['role_id' => 'PK01', 'role_name' => 'Leader Parking'],
            ['role_id' => 'PK02', 'role_name' => 'Staff Parking'],
            // ------------------------------------

            // Peran Security
            ['role_id' => 'SC01', 'role_name' => 'Leader Security'],
            ['role_id' => 'SC02', 'role_name' => 'Staff Security'],

            // Peran Warehouse
            ['id' => 'WH01', 'name_role' => 'Leader Warehouse'],
            ['id' => 'WH02', 'name_role' => 'Staff Warehouse'],
        ]);
    }
}
