<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashExpense extends Model
{
    protected $fillable = [
        'shift_id', 'user_id', 'description', 'amount',
        'ingredient_id', 'purchase_qty', 'purchase_unit', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'purchase_qty' => 'decimal:2',
        ];
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * Check if this expense is linked to an ingredient purchase.
     */
    public function isIngredientPurchase(): bool
    {
        return !is_null($this->ingredient_id) && !is_null($this->purchase_qty);
    }
}
