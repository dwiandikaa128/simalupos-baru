<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = ['product_id', 'name', 'unit', 'current_qty', 'min_qty'];

    protected function casts(): array
    {
        return [
            'current_qty' => 'decimal:2',
            'min_qty' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_qty <= $this->min_qty;
    }
}
