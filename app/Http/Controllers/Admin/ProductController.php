<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\ProductVariant;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'variants', 'recipes.ingredient');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->orderBy('sort_order')->paginate(15);
        $categories = Category::active()->orderBy('sort_order')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        $ingredients = Ingredient::active()->orderBy('name')->get();
        return view('admin.products.create', compact('categories', 'ingredients'));
    }

    public function store(Request $request)
    {
        // Clean empty variant inputs before validation
        if ($request->has('variants')) {
            $cleanedVariants = array_filter($request->input('variants'), function($v) {
                return !empty($v['name']);
            });
            if (empty($cleanedVariants)) {
                $request->request->remove('variants');
            } else {
                $request->merge(['variants' => $cleanedVariants]);
            }
        }

        // Clean empty recipe inputs before validation
        if ($request->has('recipes')) {
            $cleanedRecipes = array_filter($request->input('recipes'), function($r) {
                return !empty($r['ingredient_id']) && !empty($r['quantity']);
            });
            if (empty($cleanedRecipes)) {
                $request->request->remove('recipes');
            } else {
                $request->merge(['recipes' => $cleanedRecipes]);
            }
        }

        $validated = $request->validate([
            'name' => 'required|max:150',
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'photo' => 'nullable|image|max:2048',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required_with:variants|string',
            'variants.*.price_modifier' => 'required_with:variants|numeric',
            'recipes' => 'nullable|array',
            'recipes.*.ingredient_id' => 'nullable|exists:ingredients,id',
            'recipes.*.product_variant_id' => 'nullable|integer',
            'recipes.*.quantity' => 'nullable|numeric|min:0.01',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('products', 'public');
        }

        $product = Product::create($validated);

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                if (!empty($variant['name'])) {
                    $product->variants()->create($variant);
                }
            }
        }

        $this->syncRecipes($request, $product);

        ActivityLog::log('create_product', "Menambah produk: {$product->name}", $product);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $product->load('variants', 'recipes.ingredient');
        $categories = Category::active()->orderBy('sort_order')->get();
        $ingredients = Ingredient::active()->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories', 'ingredients'));
    }

    public function update(Request $request, Product $product)
    {
        // Clean empty variant inputs before validation
        if ($request->has('variants')) {
            $cleanedVariants = array_filter($request->input('variants'), function($v) {
                return !empty($v['name']);
            });
            if (empty($cleanedVariants)) {
                $request->request->remove('variants');
            } else {
                $request->merge(['variants' => $cleanedVariants]);
            }
        }

        // Clean empty recipe inputs before validation
        if ($request->has('recipes')) {
            $cleanedRecipes = array_filter($request->input('recipes'), function($r) {
                return !empty($r['ingredient_id']) && !empty($r['quantity']);
            });
            if (empty($cleanedRecipes)) {
                $request->request->remove('recipes');
            } else {
                $request->merge(['recipes' => $cleanedRecipes]);
            }
        }

        $validated = $request->validate([
            'name' => 'required|max:150',
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'photo' => 'nullable|image|max:2048',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.name' => 'nullable|string',
            'variants.*.price_modifier' => 'nullable|numeric',
            'recipes' => 'nullable|array',
            'recipes.*.ingredient_id' => 'nullable|exists:ingredients,id',
            'recipes.*.product_variant_id' => 'nullable|integer',
            'recipes.*.quantity' => 'nullable|numeric|min:0.01',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('products', 'public');
        }

        $product->update($validated);

        // Update variants
        if ($request->has('variants')) {
            $keptVariantIds = [];
            foreach ($request->variants as $variant) {
                if (!empty($variant['name'])) {
                    $payload = [
                        'name' => $variant['name'],
                        'price_modifier' => $variant['price_modifier'] ?? 0,
                    ];

                    if (!empty($variant['id']) && $product->variants()->whereKey($variant['id'])->exists()) {
                        $product->variants()->whereKey($variant['id'])->update($payload);
                        $keptVariantIds[] = (int) $variant['id'];
                    } else {
                        $keptVariantIds[] = $product->variants()->create($payload)->id;
                    }
                }
            }

            $product->variants()
                ->when($keptVariantIds, fn($query) => $query->whereNotIn('id', $keptVariantIds))
                ->delete();
        }

        $product->refresh();
        $this->syncRecipes($request, $product);

        ActivityLog::log('edit_product', "Mengedit produk: {$product->name}", $product);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diupdate!');
    }

    public function destroy(Product $product)
    {
        ActivityLog::log('delete_product', "Menghapus produk: {$product->name}", $product);
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function toggleAvailability(Product $product)
    {
        $product->update(['is_available' => !$product->is_available]);
        return back()->with('success', 'Status ketersediaan produk diubah!');
    }

    private function syncRecipes(Request $request, Product $product): void
    {
        $product->recipes()->delete();

        foreach ($request->input('recipes', []) as $recipe) {
            if (empty($recipe['ingredient_id']) || empty($recipe['quantity'])) {
                continue;
            }

            $product->recipes()->create([
                'ingredient_id' => $recipe['ingredient_id'],
                'product_variant_id' => !empty($recipe['product_variant_id']) ? $recipe['product_variant_id'] : null,
                'quantity' => $recipe['quantity'],
            ]);
        }
    }
}
