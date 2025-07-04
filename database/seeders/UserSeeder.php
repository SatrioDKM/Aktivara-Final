<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            // Superadmin
            [
                'name' => 'Admin Utama',
                'email' => 'superadmin@manpro.app',
                'password' => Hash::make('password'),
                'role_id' => 'SA00',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Manager
            [
                'name' => 'Bapak Manajer',
                'email' => 'manager@manpro.app',
                'password' => Hash::make('password'),
                'role_id' => 'MG00',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Leaders
            [
                'name' => 'Andi (Leader HK)',
                'email' => 'leader.hk@manpro.app',
                'password' => Hash::make('password'),
                'role_id' => 'HK01',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Tono (Leader Teknisi)',
                'email' => 'leader.tk@manpro.app',
                'password' => Hash::make('password'),
                'role_id' => 'TK01',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Staff
            [
                'name' => 'Budi (Staff HK)',
                'email' => 'staff.hk.budi@manpro.app',
                'password' => Hash::make('password'),
                'role_id' => 'HK02',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Citra (Staff HK)',
                'email' => 'staff.hk.citra@manpro.app',
                'password' => Hash::make('password'),
                'role_id' => 'HK02',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Dodi (Staff Teknisi)',
                'email' => 'staff.tk.dodi@manpro.app',
                'password' => Hash::make('password'),
                'role_id' => 'TK02',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
