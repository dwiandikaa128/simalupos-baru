<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngredientPurchase extends Model
{
    protected $fillable = [
        'ingredient_id', 'user_id', 'quantity', 'total_cost', 'unit_cost',
        'supplier', 'notes', 'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'unit_cost' => 'decimal:4',
            'purchased_at' => 'datetime',
        ];
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
