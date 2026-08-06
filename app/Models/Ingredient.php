<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = [
        'ingredient_category_id', 'name', 'unit', 'current_qty',
        'min_qty', 'cost_per_base_unit', 'is_active', 'track_stock',
    ];

    protected function casts(): array
    {
        return [
            'current_qty' => 'decimal:2',
            'min_qty' => 'decimal:2',
            'cost_per_base_unit' => 'decimal:4',
            'is_active' => 'boolean',
            'track_stock' => 'boolean',
        ];
    }

    public function unitCost(): float
    {
        return (float) $this->cost_per_base_unit;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(IngredientCategory::class, 'ingredient_category_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(IngredientPurchase::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class);
    }

    public function isLowStock(): bool
    {
        if (!$this->track_stock) {
            return false;
        }

        return $this->current_qty <= $this->min_qty;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
