<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = [
        'user_id', 'employee_name', 'shift_name', 'started_at', 'ended_at',
        'opening_cash', 'closing_cash', 'actual_closing_cash',
        'net_revenue', 'cash_left_for_next_shift', 'total_expenses',
        'total_sales', 'total_transactions', 'notes', 'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'opening_cash' => 'decimal:2',
            'closing_cash' => 'decimal:2',
            'actual_closing_cash' => 'decimal:2',
            'net_revenue' => 'decimal:2',
            'cash_left_for_next_shift' => 'decimal:2',
            'total_sales' => 'decimal:2',
            'total_expenses' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(CashExpense::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
