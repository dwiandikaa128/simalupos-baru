<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetHppData extends Command
{
    protected $signature = 'hpp:reset {--force : Skip confirmation}';
    protected $description = 'Reset semua data bahan baku, resep, inventory, dan HPP untuk testing ulang';

    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('⚠️  Ini akan menghapus SEMUA data bahan baku, resep, pembelian, inventory, waste, stock opname, dan HPP pada order items. Lanjutkan?')) {
            $this->info('Dibatalkan.');
            return 0;
        }

        $this->info('🔄 Memulai reset data HPP...');

        DB::transaction(function () {
            // 1. Reset HPP fields on order_items (keep orders intact)
            $affectedOrderItems = DB::table('order_items')
                ->whereNotNull('hpp_per_item')
                ->orWhere('total_hpp', '>', 0)
                ->update([
                    'hpp_per_item' => 0,
                    'total_hpp' => 0,
                    'gross_profit' => 0,
                ]);
            $this->line("  ✅ Reset HPP di {$affectedOrderItems} order items");

            // 2. Delete inventory movements
            $movements = DB::table('inventory_movements')->count();
            DB::table('inventory_movements')->delete();
            $this->line("  ✅ Hapus {$movements} inventory movements");

            // 3. Delete stock opnames
            $opnames = DB::table('stock_opnames')->count();
            DB::table('stock_opnames')->delete();
            $this->line("  ✅ Hapus {$opnames} stock opnames");

            // 4. Delete ingredient purchases
            $purchases = DB::table('ingredient_purchases')->count();
            DB::table('ingredient_purchases')->delete();
            $this->line("  ✅ Hapus {$purchases} ingredient purchases");

            // 5. Delete product recipes
            $recipes = DB::table('product_recipes')->count();
            DB::table('product_recipes')->delete();
            $this->line("  ✅ Hapus {$recipes} product recipes");

            // 6. Delete stock reports related to ingredients
            $stockReports = DB::table('stock_reports')
                ->whereNotNull('ingredient_id')
                ->count();
            DB::table('stock_reports')
                ->whereNotNull('ingredient_id')
                ->delete();
            $this->line("  ✅ Hapus {$stockReports} stock reports (ingredient)");

            // 7. Delete all ingredients
            $ingredients = DB::table('ingredients')->count();
            DB::table('ingredients')->delete();
            $this->line("  ✅ Hapus {$ingredients} ingredients");

            // 8. Delete ingredient categories
            $categories = DB::table('ingredient_categories')->count();
            DB::table('ingredient_categories')->delete();
            $this->line("  ✅ Hapus {$categories} ingredient categories");
        });

        $this->newLine();
        $this->info('🎉 Reset data HPP selesai! Anda bisa mulai mengisi ulang data bahan baku dari awal.');
        $this->newLine();
        $this->table(
            ['Data', 'Status'],
            [
                ['Ingredient Categories', '🗑️ Dihapus'],
                ['Ingredients', '🗑️ Dihapus'],
                ['Ingredient Purchases', '🗑️ Dihapus'],
                ['Product Recipes', '🗑️ Dihapus'],
                ['Inventory Movements', '🗑️ Dihapus'],
                ['Stock Opnames', '🗑️ Dihapus'],
                ['Stock Reports (Ingredient)', '🗑️ Dihapus'],
                ['Order Items HPP', '🔄 Reset ke 0'],
                ['Orders', '✅ Tetap ada'],
                ['Products', '✅ Tetap ada'],
                ['Users', '✅ Tetap ada'],
            ]
        );

        return 0;
    }
}
