<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'user_id', 'period_month', 'base_salary', 'allowance',
        'deduction', 'bonus', 'total_salary', 'days_present',
        'total_working_days', 'payment_status', 'paid_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'base_salary' => 'decimal:2',
            'allowance' => 'decimal:2',
            'deduction' => 'decimal:2',
            'bonus' => 'decimal:2',
            'total_salary' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calculateTotal(): float
    {
        return (float) $this->base_salary + (float) $this->allowance
             - (float) $this->deduction + (float) $this->bonus;
    }

    public function scopeForMonth($query, string $month)
    {
        return $query->where('period_month', $month);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }
}
