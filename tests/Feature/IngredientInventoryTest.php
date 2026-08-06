<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\StockReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_ingredient_and_record_purchase(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)
            ->post(route('admin.ingredient-categories.store'), [
                'name' => 'Dairy',
                'sort_order' => 1,
            ])
            ->assertRedirect();

        $category = IngredientCategory::first();

        $this->actingAs($admin)
            ->post(route('admin.ingredients.store'), [
                'ingredient_category_id' => $category->id,
                'name' => 'Susu UHT',
                'unit' => 'ml',
                'current_qty' => 0,
                'min_qty' => 1000,
                'cost_per_base_unit' => 7500,
            ])
            ->assertRedirect();

        $ingredient = Ingredient::first();

        $this->actingAs($admin)
            ->post(route('admin.ingredient-purchases.store'), [
                'ingredient_id' => $ingredient->id,
                'quantity' => 24000,
                'notes' => '1/2 dus',
            ])
            ->assertRedirect();

        $ingredient->refresh();

        $this->assertSame('24000.00', $ingredient->current_qty);
        $this->assertDatabaseHas('ingredient_purchases', [
            'ingredient_id' => $ingredient->id,
            'unit_cost' => 7.5,
        ]);
        $this->assertDatabaseHas('inventory_movements', [
            'ingredient_id' => $ingredient->id,
            'type' => 'purchase',
            'quantity' => 24000,
        ]);
    }

    public function test_paid_order_deducts_recipe_stock_and_snapshots_hpp_once(): void
    {
        $barista = User::factory()->create(['role' => 'barista', 'is_active' => true]);
        $category = Category::create(['name' => 'Kopi Susu', 'slug' => 'kopi-susu']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Kopi Susu Simalu',
            'slug' => 'kopi-susu-simalu-test',
            'base_price' => 25000,
        ]);
        $ingredientCategory = IngredientCategory::create(['name' => 'Dairy', 'slug' => 'dairy']);
        $ingredient = Ingredient::create([
            'ingredient_category_id' => $ingredientCategory->id,
            'name' => 'Susu UHT',
            'unit' => 'ml',
            'current_qty' => 100,
            'min_qty' => 80,
            'cost_per_base_unit' => 2000,
        ]);
        ProductRecipe::create([
            'product_id' => $product->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 10,
        ]);
        $order = Order::create([
            'order_number' => '#ORD-TEST-001',
            'user_id' => $barista->id,
            'order_type' => 'dine_in',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => 75000,
            'total_amount' => 75000,
        ]);
        $item = $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 25000,
            'quantity' => 3,
            'subtotal' => 75000,
        ]);

        $this->actingAs($barista)
            ->patchJson(route('pos.orders.pay', $order), [
                'payment_method' => 'cash',
                'amount_paid' => 75000,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $ingredient->refresh();
        $item->refresh();

        $this->assertSame('70.00', $ingredient->current_qty);
        $this->assertSame('20.00', $item->hpp_per_item);
        $this->assertSame('60.00', $item->total_hpp);
        $this->assertSame('74940.00', $item->gross_profit);
        $this->assertSame(1, InventoryMovement::where('order_id', $order->id)->where('type', 'sale')->count());
        $this->assertSame(1, StockReport::where('ingredient_id', $ingredient->id)->where('source', 'automatic')->count());

        $this->actingAs($barista)
            ->patchJson(route('pos.orders.pay', $order), [
                'payment_method' => 'cash',
                'amount_paid' => 75000,
            ])
            ->assertOk();

        $this->assertSame('70.00', $ingredient->fresh()->current_qty);
        $this->assertSame(1, InventoryMovement::where('order_id', $order->id)->where('type', 'sale')->count());
        $this->assertSame(1, StockReport::where('ingredient_id', $ingredient->id)->where('source', 'automatic')->count());
    }

    public function test_ingredient_categories_do_not_appear_in_pos_categories(): void
    {
        $barista = User::factory()->create(['role' => 'barista', 'is_active' => true]);
        Category::create(['name' => 'Kopi Susu', 'slug' => 'kopi-susu']);
        IngredientCategory::create(['name' => 'Kondimen Rahasia', 'slug' => 'kondimen-rahasia']);

        $this->actingAs($barista)
            ->get(route('pos.index'))
            ->assertOk()
            ->assertSee('Kopi Susu')
            ->assertDontSee('Kondimen Rahasia');
    }

    public function test_admin_can_delete_empty_ingredient_category_but_not_category_with_ingredients(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $emptyCategory = IngredientCategory::create(['name' => 'Empty', 'slug' => 'empty']);
        $usedCategory = IngredientCategory::create(['name' => 'Used', 'slug' => 'used']);

        Ingredient::create([
            'ingredient_category_id' => $usedCategory->id,
            'name' => 'Susu UHT',
            'unit' => 'ml',
            'current_qty' => 0,
            'min_qty' => 0,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.ingredient-categories.destroy', $usedCategory))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('ingredient_categories', ['id' => $usedCategory->id]);

        $this->actingAs($admin)
            ->delete(route('admin.ingredient-categories.destroy', $emptyCategory))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('ingredient_categories', ['id' => $emptyCategory->id]);
    }
}
