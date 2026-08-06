<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Kopi Susu', 'slug' => 'kopi-susu', 'icon' => 'coffee', 'sort_order' => 1],
            ['name' => 'Manual Brew', 'slug' => 'manual-brew', 'icon' => 'filter_alt', 'sort_order' => 2],
            ['name' => 'Non-Kopi', 'slug' => 'non-kopi', 'icon' => 'local_cafe', 'sort_order' => 3],
            ['name' => 'Pastry', 'slug' => 'pastry', 'icon' => 'bakery_dining', 'sort_order' => 4],
            ['name' => 'Merchandise', 'slug' => 'merchandise', 'icon' => 'shopping_bag', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
