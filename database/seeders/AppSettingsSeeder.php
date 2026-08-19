<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'shop_name', 'value' => 'SimaluCoffee', 'group' => 'general'],
            ['key' => 'shop_address', 'value' => 'Jl. Sudirman No. 1, Jakarta', 'group' => 'general'],
            ['key' => 'shop_phone', 'value' => '021-1234567', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'IDR', 'group' => 'general'],
            ['key' => 'tax_rate', 'value' => '10', 'group' => 'tax'],
            ['key' => 'tax_enabled', 'value' => 'true', 'group' => 'tax'],
            ['key' => 'receipt_header', 'value' => 'SimaluCoffee - Specialty Coffee', 'group' => 'receipt'],
            ['key' => 'receipt_footer', 'value' => 'Terima kasih telah berkunjung! ☕', 'group' => 'receipt'],
            ['key' => 'receipt_show_logo', 'value' => 'true', 'group' => 'receipt'],
            ['key' => 'show_order_number', 'value' => 'true', 'group' => 'receipt'],
            ['key' => 'qris_merchant_id', 'value' => 'ID1234567890', 'group' => 'payment'],
            ['key' => 'cash_enabled', 'value' => 'true', 'group' => 'payment'],
            ['key' => 'qris_enabled', 'value' => 'true', 'group' => 'payment'],
            ['key' => 'debit_enabled', 'value' => 'true', 'group' => 'payment'],
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
