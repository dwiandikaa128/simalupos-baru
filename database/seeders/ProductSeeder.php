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
        $kopiSusu = Category::where('slug', 'kopi-susu')->first();
        $manualBrew = Category::where('slug', 'manual-brew')->first();
        $nonKopi = Category::where('slug', 'non-kopi')->first();
        $pastry = Category::where('slug', 'pastry')->first();
        $merchandise = Category::where('slug', 'merchandise')->first();

        // Kopi Susu
        $products = [
            ['category_id' => $kopiSusu->id, 'name' => 'Kopi Susu Simalu', 'slug' => 'kopi-susu-simalu', 'base_price' => 25000, 'description' => 'Signature kopi susu dengan campuran gula aren', 'is_featured' => true],
            ['category_id' => $kopiSusu->id, 'name' => 'Es Kopi Latte', 'slug' => 'es-kopi-latte', 'base_price' => 28000, 'description' => 'Espresso dengan susu segar'],
            ['category_id' => $kopiSusu->id, 'name' => 'Cappuccino', 'slug' => 'cappuccino', 'base_price' => 30000, 'description' => 'Espresso, steamed milk, dan foam lembut'],
            ['category_id' => $kopiSusu->id, 'name' => 'Caramel Macchiato', 'slug' => 'caramel-macchiato', 'base_price' => 32000, 'description' => 'Espresso dengan vanilla syrup dan caramel drizzle'],
            ['category_id' => $kopiSusu->id, 'name' => 'Mocha Latte', 'slug' => 'mocha-latte', 'base_price' => 30000, 'description' => 'Perpaduan espresso, cokelat, dan susu'],

            // Manual Brew
            ['category_id' => $manualBrew->id, 'name' => 'V60 Drip', 'slug' => 'v60-drip', 'base_price' => 35000, 'description' => 'Single origin pour over V60'],
            ['category_id' => $manualBrew->id, 'name' => 'Chemex', 'slug' => 'chemex', 'base_price' => 38000, 'description' => 'Clean and bright coffee dari Chemex'],
            ['category_id' => $manualBrew->id, 'name' => 'French Press', 'slug' => 'french-press', 'base_price' => 32000, 'description' => 'Full-bodied immersion brew'],
            ['category_id' => $manualBrew->id, 'name' => 'Aeropress', 'slug' => 'aeropress', 'base_price' => 33000, 'description' => 'Versatile dan smooth brew'],
            ['category_id' => $manualBrew->id, 'name' => 'Cold Brew', 'slug' => 'cold-brew', 'base_price' => 30000, 'description' => '18 jam cold extraction'],

            // Non-Kopi
            ['category_id' => $nonKopi->id, 'name' => 'Matcha Latte', 'slug' => 'matcha-latte', 'base_price' => 28000, 'description' => 'Premium matcha dengan susu segar'],
            ['category_id' => $nonKopi->id, 'name' => 'Cokelat Panas', 'slug' => 'cokelat-panas', 'base_price' => 25000, 'description' => 'Rich hot chocolate'],
            ['category_id' => $nonKopi->id, 'name' => 'Teh Tarik', 'slug' => 'teh-tarik', 'base_price' => 20000, 'description' => 'Teh susu khas Malaysia'],
            ['category_id' => $nonKopi->id, 'name' => 'Lemon Tea', 'slug' => 'lemon-tea', 'base_price' => 18000, 'description' => 'Teh segar dengan lemon'],
            ['category_id' => $nonKopi->id, 'name' => 'Jus Jeruk Segar', 'slug' => 'jus-jeruk', 'base_price' => 22000, 'description' => 'Fresh squeezed orange juice'],

            // Pastry
            ['category_id' => $pastry->id, 'name' => 'Croissant Butter', 'slug' => 'croissant-butter', 'base_price' => 25000, 'description' => 'Croissant renyah dengan butter premium'],
            ['category_id' => $pastry->id, 'name' => 'Banana Bread', 'slug' => 'banana-bread', 'base_price' => 22000, 'description' => 'Homemade banana bread lembut'],
            ['category_id' => $pastry->id, 'name' => 'Cookies & Cream', 'slug' => 'cookies-cream', 'base_price' => 18000, 'description' => 'Cookies dengan topping cream'],
            ['category_id' => $pastry->id, 'name' => 'Cheese Cake', 'slug' => 'cheese-cake', 'base_price' => 30000, 'description' => 'New York style cheesecake'],
            ['category_id' => $pastry->id, 'name' => 'Brownies', 'slug' => 'brownies', 'base_price' => 20000, 'description' => 'Fudgy chocolate brownies'],

            // Merchandise
            ['category_id' => $merchandise->id, 'name' => 'Tumbler Simalu', 'slug' => 'tumbler-simalu', 'base_price' => 120000, 'description' => 'Tumbler eksklusif SimaluCoffee 350ml'],
            ['category_id' => $merchandise->id, 'name' => 'Tote Bag Simalu', 'slug' => 'tote-bag-simalu', 'base_price' => 85000, 'description' => 'Canvas tote bag dengan desain minimal'],
            ['category_id' => $merchandise->id, 'name' => 'Biji Kopi 250gr', 'slug' => 'biji-kopi-250', 'base_price' => 95000, 'description' => 'House blend roasted beans'],
            ['category_id' => $merchandise->id, 'name' => 'Drip Bag Box (10pcs)', 'slug' => 'drip-bag-box', 'base_price' => 75000, 'description' => 'Single serve drip coffee'],
            ['category_id' => $merchandise->id, 'name' => 'Mug Keramik', 'slug' => 'mug-keramik', 'base_price' => 65000, 'description' => 'Handmade ceramic mug 200ml'],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);

            // Add variants for drink products (not merchandise/pastry)
            if (in_array($product->category_id, [$kopiSusu->id, $manualBrew->id, $nonKopi->id])) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => 'Regular',
                    'price_modifier' => 0,
                    'is_default' => true,
                ]);
                ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => 'Large',
                    'price_modifier' => 5000,
                    'is_default' => false,
                ]);
            }
        }
    }
}
