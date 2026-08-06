<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalCost extends Model
{
    protected $fillable = [
        'name', 'category', 'amount', 'period_month',
        'is_recurring', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_recurring' => 'boolean',
        ];
    }

    public const CATEGORIES = [
        'utilities' => 'Utilitas',
        'rent' => 'Sewa',
        'maintenance' => 'Perawatan',
        'supplies' => 'Perlengkapan',
        'other' => 'Lain-lain',
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function scopeForMonth($query, string $month)
    {
        return $query->where('period_month', $month);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }
}
