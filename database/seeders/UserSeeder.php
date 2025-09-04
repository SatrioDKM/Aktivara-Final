<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // Superadmin & Manager
            ['name' => 'Superadmin', 'email' => 'superadmin@example.com', 'role_id' => 'SA00', 'password' => Hash::make('password'), 'telegram_chat_id' => '648052160'],
            ['name' => 'Manager', 'email' => 'manager@example.com', 'role_id' => 'MG00', 'password' => Hash::make('password'), 'telegram_chat_id' => '648052160'],

            // Leaders
            ['name' => 'Leader Housekeeping', 'email' => 'leader.hk@example.com', 'role_id' => 'HK01', 'password' => Hash::make('password'), 'telegram_chat_id' => '648052160'],
            ['name' => 'Leader Teknisi', 'email' => 'leader.tk@example.com', 'role_id' => 'TK01', 'password' => Hash::make('password'), 'telegram_chat_id' => '648052160'],
            ['name' => 'Leader Security', 'email' => 'leader.sc@example.com', 'role_id' => 'SC01', 'password' => Hash::make('password'), 'telegram_chat_id' => '648052160'],
            ['name' => 'Leader Parking', 'email' => 'leader.pk@example.com', 'role_id' => 'PK01', 'password' => Hash::make('password'), 'telegram_chat_id' => '648052160'],

            // Staff
            ['name' => 'Staff Housekeeping', 'email' => 'staff.hk@example.com', 'role_id' => 'HK02', 'password' => Hash::make('password'), 'telegram_chat_id' => '648052160'],
            ['name' => 'Staff Teknisi', 'email' => 'staff.tk@example.com', 'role_id' => 'TK02', 'password' => Hash::make('password'), 'telegram_chat_id' => '648052160'],
            ['name' => 'Staff Security', 'email' => 'staff.sc@example.com', 'role_id' => 'SC02', 'password' => Hash::make('password'), 'telegram_chat_id' => '648052160'],
            ['name' => 'Staff Parking', 'email' => 'staff.pk@example.com', 'role_id' => 'PK02', 'password' => Hash::make('password'), 'telegram_chat_id' => '648052160'],
            ['name' => 'Leader Warehouse', 'email' => 'leader.wh@example.com', 'role_id' => 'WH01', 'password' => Hash::make('password'), 'telegram_chat_id' => '648052160'],
            ['name' => 'Staff Warehouse', 'email' => 'staff.wh@example.com', 'role_id' => 'WH02', 'password' => Hash::make('password'), 'telegram_chat_id' => '648052160'],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
