<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDataCommand extends Command
{
    protected $signature = 'app:reset-data {--force : Paksa jalankan tanpa konfirmasi}';
    protected $description = 'Menghapus seluruh data dummy (menu, kategori, resep, transaksi, shift, absensi, dll) untuk persiapan produksi, sambil MEMPERTAHANKAN akun admin & pegawai.';

    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('APAKAH ANDA YAKIN? Perintah ini akan menghapus SELURUH data dummy (menu, varian, bahan baku, transaksi penjualan, dll) dan MEMPERTAHANKAN data akun user admin & pegawai.')) {
            $this->info('Operasi dibatalkan.');
            return 0;
        }

        $this->info('🔄 Memulai pembersihan data dummy...');

        Schema::disableForeignKeyConstraints();

        $tables = [
            'order_items', 'orders', 'cash_expenses', 'shifts', 'attendances',
            'inventory_movements', 'stock_reports', 'stock_opnames', 'ingredient_purchases',
            'product_recipes', 'ingredients', 'ingredient_categories', 'stocks',
            'product_variants', 'products', 'categories',
            'promotion_items', 'promotions', 'vouchers',
            'operational_costs', 'payrolls', 'activity_logs'
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        $this->info('✅ Seluruh data dummy berhasil dibersihkan!');
        $this->info('✅ Data Akun Admin & Pegawai tetap DIPERTAHANKAN.');
        $this->info('🎉 Aplikasi siap diisi dengan menu produk & bahan baku baru!');

        return 0;
    }
}
