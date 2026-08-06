<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'value', 'min_purchase',
        'max_discount', 'usage_limit', 'used_count',
        'is_active', 'valid_from', 'valid_until',
        'batch_group', 'redeemed_at', 'redeemed_by_order_id',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_purchase' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'is_active' => 'boolean',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'redeemed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'redeemed_by_order_id');
    }

    public function isRedeemed(): bool
    {
        return !is_null($this->redeemed_at);
    }

    public function isValid(float $subtotal = 0): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Voucher tidak aktif'];
        }

        $today = Carbon::today();
        if ($today->lt($this->valid_from) || $today->gt($this->valid_until)) {
            return ['valid' => false, 'message' => 'Voucher sudah kadaluarsa atau belum berlaku'];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Voucher sudah mencapai batas penggunaan'];
        }

        // For batch vouchers (usage_limit = 1), check if already redeemed
        if ($this->isRedeemed()) {
            return ['valid' => false, 'message' => 'Kode voucher ini sudah pernah ditukarkan'];
        }

        if ($subtotal < $this->min_purchase) {
            return ['valid' => false, 'message' => 'Minimum pembelian '.format_rupiah($this->min_purchase)];
        }

        return ['valid' => true, 'message' => 'Voucher berhasil diterapkan'];
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            $discount = $subtotal * ($this->value / 100);
            if ($this->max_discount !== null) {
                $discount = min($discount, $this->max_discount);
            }
            return $discount;
        }

        return min($this->value, $subtotal);
    }

    public function markAsRedeemed(int $orderId): void
    {
        $this->update([
            'redeemed_at' => now(),
            'redeemed_by_order_id' => $orderId,
        ]);
    }
}
