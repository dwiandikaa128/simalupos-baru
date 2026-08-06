<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            BaristaSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            AppSettingsSeeder::class,
            VoucherSeeder::class,
        ]);
    }
}
