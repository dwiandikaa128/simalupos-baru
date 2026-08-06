<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'photo',
        'base_price', 'is_available', 'is_featured', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function hasEnoughRecipeStock(): bool
    {
        $recipes = $this->relationLoaded('recipes')
            ? $this->recipes
            : $this->recipes()->with('ingredient')->get();

        foreach ($recipes->whereNull('product_variant_id') as $recipe) {
            $ingredient = $recipe->ingredient;

            if (!$ingredient || !$ingredient->track_stock) {
                continue;
            }

            if ((float) $recipe->quantity > (float) $ingredient->current_qty) {
                return false;
            }
        }

        return true;
    }

    public function isEffectivelyAvailable(): bool
    {
        return $this->is_available && $this->hasEnoughRecipeStock();
    }

    public function unavailableRecipeIngredients()
    {
        $recipes = $this->relationLoaded('recipes')
            ? $this->recipes
            : $this->recipes()->with('ingredient')->get();

        return $recipes->whereNull('product_variant_id')->filter(function ($recipe) {
            $ingredient = $recipe->ingredient;

            return $ingredient
                && $ingredient->track_stock
                && (float) $recipe->quantity > (float) $ingredient->current_qty;
        });
    }

    public function getEffectivePrice(?ProductVariant $variant = null): float
    {
        $price = $this->base_price;
        if ($variant) {
            $price += $variant->price_modifier;
        }
        return $price;
    }
}
