<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');

        $products = [
            // 1. espresso base
            ['category_slug' => 'espresso-base', 'name' => 'Espresso', 'base_price' => 15000],
            ['category_slug' => 'espresso-base', 'name' => 'Americano', 'base_price' => 18000],
            ['category_slug' => 'espresso-base', 'name' => 'Piccolo', 'base_price' => 20000],
            ['category_slug' => 'espresso-base', 'name' => 'Latte', 'base_price' => 22000],
            ['category_slug' => 'espresso-base', 'name' => 'Cappuccino', 'base_price' => 22000],
            ['category_slug' => 'espresso-base', 'name' => 'Mocha', 'base_price' => 25000],
            ['category_slug' => 'espresso-base', 'name' => 'Caramel Latte', 'base_price' => 25000],
            ['category_slug' => 'espresso-base', 'name' => 'Salt Butterscotch', 'base_price' => 25000],
            ['category_slug' => 'espresso-base', 'name' => 'Vanilla Latte', 'base_price' => 25000],
            ['category_slug' => 'espresso-base', 'name' => 'Kopi Simalu', 'base_price' => 25000, 'is_featured' => true],
            ['category_slug' => 'espresso-base', 'name' => 'Macchiato', 'base_price' => 20000],

            // 2. manual
            ['category_slug' => 'manual', 'name' => 'Tubruk', 'base_price' => 15000],
            ['category_slug' => 'manual', 'name' => 'Vietnam', 'base_price' => 15000],
            ['category_slug' => 'manual', 'name' => 'V60', 'base_price' => 20000],
            ['category_slug' => 'manual', 'name' => 'Japanese', 'base_price' => 23000],

            // 3. non coffee
            ['category_slug' => 'non-coffee', 'name' => 'Tea', 'base_price' => 10000],
            ['category_slug' => 'non-coffee', 'name' => 'Leci Tea', 'base_price' => 18000],
            ['category_slug' => 'non-coffee', 'name' => 'Peach Tea', 'base_price' => 18000],
            ['category_slug' => 'non-coffee', 'name' => 'Lemmon Tea', 'base_price' => 18000],
            ['category_slug' => 'non-coffee', 'name' => 'Milk', 'base_price' => 13000],
            ['category_slug' => 'non-coffee', 'name' => 'Milk Shake Strawberry', 'base_price' => 23000],
            ['category_slug' => 'non-coffee', 'name' => 'Milk Shake Chocolate', 'base_price' => 23000],
            ['category_slug' => 'non-coffee', 'name' => 'Milk Shake Vanilla', 'base_price' => 23000],
            ['category_slug' => 'non-coffee', 'name' => 'Matcha Ceremonial', 'base_price' => 35000],
            ['category_slug' => 'non-coffee', 'name' => 'Matcha', 'base_price' => 23000],
            ['category_slug' => 'non-coffee', 'name' => 'Chocolate', 'base_price' => 23000],
            ['category_slug' => 'non-coffee', 'name' => 'Mineral Water', 'base_price' => 6000],

            // 4. pastry
            ['category_slug' => 'pastry', 'name' => 'Pain Au Chocolate', 'base_price' => 25000],
            ['category_slug' => 'pastry', 'name' => 'Croissant Almod', 'base_price' => 25000],
            ['category_slug' => 'pastry', 'name' => 'Croissant Plain', 'base_price' => 20000],
            ['category_slug' => 'pastry', 'name' => 'Cookies', 'base_price' => 15000],

            // 5. food
            ['category_slug' => 'food', 'name' => 'Rice Bowl Chiken', 'base_price' => 35000],
            ['category_slug' => 'food', 'name' => 'Rice Bowl Beef', 'base_price' => 35000],
            ['category_slug' => 'food', 'name' => 'Sandwich Chiken', 'base_price' => 30000],
            ['category_slug' => 'food', 'name' => 'Sandwich Beef', 'base_price' => 30000],
            ['category_slug' => 'food', 'name' => 'Beef Burger', 'base_price' => 35000],
            ['category_slug' => 'food', 'name' => 'Chiken Burger', 'base_price' => 35000],

            // 6. snack
            ['category_slug' => 'snack', 'name' => 'Snack Platter', 'base_price' => 35000],
            ['category_slug' => 'snack', 'name' => 'French Fries', 'base_price' => 15000],
            ['category_slug' => 'snack', 'name' => 'Sosis', 'base_price' => 15000],
            ['category_slug' => 'snack', 'name' => 'Nugget', 'base_price' => 15000],
            ['category_slug' => 'snack', 'name' => 'Pangsit', 'base_price' => 15000],
            ['category_slug' => 'snack', 'name' => 'Risol Chocolate', 'base_price' => 20000],
            ['category_slug' => 'snack', 'name' => 'Risol Matcha', 'base_price' => 20000],
            ['category_slug' => 'snack', 'name' => 'Risol Mayo', 'base_price' => 20000],

            // 7. wave series
            ['category_slug' => 'wave-series', 'name' => 'Strawberry Peach', 'base_price' => 25000],
            ['category_slug' => 'wave-series', 'name' => 'Guava Wave', 'base_price' => 25000],
            ['category_slug' => 'wave-series', 'name' => 'Orange Wave', 'base_price' => 25000],

            // 8. beans
            ['category_slug' => 'beans', 'name' => 'Robusta Pupuan 200 gram', 'base_price' => 50000],
            ['category_slug' => 'beans', 'name' => 'Arabica Bajawa 200 gram', 'base_price' => 70000],
            ['category_slug' => 'beans', 'name' => 'Arabica Kintamani 200 gram', 'base_price' => 70000],
        ];

        foreach ($products as $productData) {
            $categorySlug = $productData['category_slug'];
            unset($productData['category_slug']);
            
            if (isset($categories[$categorySlug])) {
                $productData['category_id'] = $categories[$categorySlug]->id;
                $productData['slug'] = \Illuminate\Support\Str::slug($productData['name']);
                
                $product = Product::updateOrCreate(['slug' => $productData['slug']], $productData);

                // Add variants for drink products
                if (in_array($categorySlug, ['espresso-base', 'manual', 'non-coffee', 'wave-series'])) {
                    // Cek jika varian belum ada
                    if ($product->variants()->count() == 0) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'name' => 'Regular',
                            'price_modifier' => 0,
                            'is_default' => true,
                        ]);
                    }
                }
            }
        }
    }
}
