<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::create([
            'code' => 'KOPI10',
            'name' => 'Diskon 10% Untuk Semua Menu',
            'type' => 'percentage',
            'value' => 10,
            'min_purchase' => 50000,
            'max_discount' => 25000,
            'usage_limit' => 100,
            'used_count' => 0,
            'is_active' => true,
            'valid_from' => '2026-01-01',
            'valid_until' => '2026-12-31',
        ]);
    }
}
