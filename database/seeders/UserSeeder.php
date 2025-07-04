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
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role_id' => 'SA00',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Manager
            [
                'name' => 'Bapak Manajer',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'role_id' => 'MG00',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Leaders
            [
                'name' => 'Andi (Leader HK)',
                'email' => 'leader.hk@example.com',
                'password' => Hash::make('password'),
                'role_id' => 'HK01',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Tono (Leader Teknisi)',
                'email' => 'leader.tk@example.com',
                'password' => Hash::make('password'),
                'role_id' => 'TK01',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Staff
            [
                'name' => 'Budi (Staff HK)',
                'email' => 'staff.hk.budi@example.com',
                'password' => Hash::make('password'),
                'role_id' => 'HK02',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Citra (Staff HK)',
                'email' => 'staff.hk.citra@example.com',
                'password' => Hash::make('password'),
                'role_id' => 'HK02',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Dodi (Staff Teknisi)',
                'email' => 'staff.tk.dodi@example.com',
                'password' => Hash::make('password'),
                'role_id' => 'TK02',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
