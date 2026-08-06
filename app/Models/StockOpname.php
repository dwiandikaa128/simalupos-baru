<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpname extends Model
{
    protected $fillable = [
        'ingredient_id', 'user_id', 'system_qty', 'actual_qty',
        'difference', 'adjustment_type', 'notes', 'opname_date',
    ];

    protected function casts(): array
    {
        return [
            'system_qty' => 'decimal:2',
            'actual_qty' => 'decimal:2',
            'difference' => 'decimal:2',
            'opname_date' => 'date',
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
