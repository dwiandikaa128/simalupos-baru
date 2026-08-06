<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReport extends Model
{
    protected $fillable = [
        'ingredient_id', 'reporter_name', 'item_name', 'status',
        'notes', 'source', 'is_resolved',
    ];

    protected function casts(): array
    {
        return ['is_resolved' => 'boolean'];
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
