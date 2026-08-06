<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin SimaluCoffee',
            'email' => 'admin@simalucoffee.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true,
            'joined_at' => '2024-01-01',
            'email_verified_at' => now(),
        ]);
    }
}
