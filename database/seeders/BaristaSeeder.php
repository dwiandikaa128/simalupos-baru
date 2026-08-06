<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class BaristaSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@simalucoffee.com',
            'password' => bcrypt('password'),
            'role' => 'barista',
            'pin' => '123456',
            'phone' => '081234567891',
            'is_active' => true,
            'joined_at' => '2024-03-15',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@simalucoffee.com',
            'password' => bcrypt('password'),
            'role' => 'barista',
            'pin' => '654321',
            'phone' => '081234567892',
            'is_active' => true,
            'joined_at' => '2024-06-01',
            'email_verified_at' => now(),
        ]);
    }
}
