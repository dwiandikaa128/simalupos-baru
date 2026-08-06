<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\PromotionItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::with(['items.product', 'category'])->latest()->get();
        $products = Product::available()->orderBy('name')->get();
        $categories = Category::active()->orderBy('sort_order')->get();

        return view('admin.promotions.index', compact('promotions', 'products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:combo,discount_product,discount_category',
            'discount_type' => 'nullable|required_if:type,discount_product,discount_category|in:percentage,fixed_price',
            'discount_value' => 'nullable|required_if:type,discount_product,discount_category|numeric|min:0',
            'combo_price' => 'nullable|required_if:type,combo|numeric|min:0',
            'category_id' => 'nullable|required_if:type,discount_category|exists:categories,id',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'time_start' => 'nullable',
            'time_end' => 'nullable',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'free_product_ids' => 'nullable|array',
            'free_product_ids.*' => 'exists:products,id',
        ]);

        $promotion = Promotion::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'discount_type' => $validated['discount_type'] ?? null,
            'discount_value' => $validated['discount_value'] ?? null,
            'combo_price' => $validated['combo_price'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'valid_from' => $validated['valid_from'],
            'valid_until' => $validated['valid_until'],
            'time_start' => $validated['time_start'] ?? null,
            'time_end' => $validated['time_end'] ?? null,
            'is_active' => true,
        ]);

        // Add combo/discount products
        if (!empty($validated['product_ids'])) {
            foreach ($validated['product_ids'] as $productId) {
                PromotionItem::create([
                    'promotion_id' => $promotion->id,
                    'product_id' => $productId,
                    'is_free' => false,
                ]);
            }
        }

        // Add free products (BOGO)
        if (!empty($validated['free_product_ids'])) {
            foreach ($validated['free_product_ids'] as $productId) {
                PromotionItem::create([
                    'promotion_id' => $promotion->id,
                    'product_id' => $productId,
                    'is_free' => true,
                ]);
            }
        }

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promosi berhasil dibuat!');
    }

    public function edit(Promotion $promotion)
    {
        $promotions = Promotion::with(['items.product', 'category'])->latest()->get();
        $products = Product::available()->orderBy('name')->get();
        $categories = Category::active()->orderBy('sort_order')->get();
        
        $promotion->load('items.product');
        $selectedProductIds = $promotion->items->where('is_free', false)->pluck('product_id')->toArray();
        $selectedFreeProductIds = $promotion->items->where('is_free', true)->pluck('product_id')->toArray();

        return view('admin.promotions.edit', compact('promotion', 'products', 'categories', 'selectedProductIds', 'selectedFreeProductIds'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:combo,discount_product,discount_category',
            'discount_type' => 'nullable|in:percentage,fixed_price',
            'discount_value' => 'nullable|numeric|min:0',
            'combo_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'time_start' => 'nullable|date_format:H:i,H:i:s',
            'time_end' => 'nullable|date_format:H:i,H:i:s',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'free_product_ids' => 'nullable|array',
            'free_product_ids.*' => 'exists:products,id',
        ]);

        $promotion->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'discount_type' => $validated['discount_type'] ?? null,
            'discount_value' => $validated['discount_value'] ?? null,
            'combo_price' => $validated['combo_price'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'valid_from' => $validated['valid_from'],
            'valid_until' => $validated['valid_until'],
            'time_start' => $validated['time_start'] ?? null,
            'time_end' => $validated['time_end'] ?? null,
        ]);

        // Sync products
        $promotion->items()->delete();

        if (!empty($validated['product_ids'])) {
            foreach ($validated['product_ids'] as $productId) {
                PromotionItem::create([
                    'promotion_id' => $promotion->id,
                    'product_id' => $productId,
                    'is_free' => false,
                ]);
            }
        }

        if (!empty($validated['free_product_ids'])) {
            foreach ($validated['free_product_ids'] as $productId) {
                PromotionItem::create([
                    'promotion_id' => $promotion->id,
                    'product_id' => $productId,
                    'is_free' => true,
                ]);
            }
        }

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promosi berhasil diupdate!');
    }

    public function toggleActive(Promotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);

        $status = $promotion->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.promotions.index')
            ->with('success', "Promosi berhasil {$status}!");
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promosi berhasil dihapus!');
    }
}
