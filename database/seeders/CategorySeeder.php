<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Espresso Base', 'slug' => 'espresso-base', 'icon' => 'coffee', 'sort_order' => 1],
            ['name' => 'Manual', 'slug' => 'manual', 'icon' => 'filter_alt', 'sort_order' => 2],
            ['name' => 'Non Coffee', 'slug' => 'non-coffee', 'icon' => 'local_cafe', 'sort_order' => 3],
            ['name' => 'Pastry', 'slug' => 'pastry', 'icon' => 'bakery_dining', 'sort_order' => 4],
            ['name' => 'Food', 'slug' => 'food', 'icon' => 'restaurant', 'sort_order' => 5],
            ['name' => 'Snack', 'slug' => 'snack', 'icon' => 'fastfood', 'sort_order' => 6],
            ['name' => 'Wave Series', 'slug' => 'wave-series', 'icon' => 'waves', 'sort_order' => 7],
            ['name' => 'Beans', 'slug' => 'beans', 'icon' => 'eco', 'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
