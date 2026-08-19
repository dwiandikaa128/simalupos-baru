<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'customer_id', 'customer_name', 'table_number',
        'order_type', 'status', 'payment_method', 'payment_option', 'payment_status',
        'paid_by_membership', 'change_to_membership',
        'subtotal', 'discount_amount', 'service_charge_amount', 'tax_amount', 'total_amount',
        'amount_paid', 'change_amount', 'notes', 'voucher_code',
        'held_at', 'paid_at', 'print_count',
    ];

    protected function casts(): array
    {
        return [
            'paid_by_membership' => 'decimal:2',
            'change_to_membership' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'service_charge_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'held_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateOrderNumber(): string
    {
        $today = Carbon::today()->format('Ymd');
        $lastOrder = self::where('order_number', 'like', "#ORD-{$today}-%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastOrder) {
            $lastSeq = (int) substr($lastOrder->order_number, -3);
            $sequence = $lastSeq + 1;
        }

        return sprintf('#ORD-%s-%03d', $today, $sequence);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid')->where('status', '!=', 'cancelled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }
}
