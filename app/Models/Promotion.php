<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Promotion extends Model
{
    protected $fillable = [
        'name', 'description', 'type',
        'discount_type', 'discount_value', 'combo_price',
        'category_id', 'valid_from', 'valid_until',
        'time_start', 'time_end', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'combo_price' => 'decimal:2',
            'is_active' => 'boolean',
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PromotionItem::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Check if this promotion is currently active (date + time).
     */
    public function isActiveNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = Carbon::today();
        if ($today->lt($this->valid_from) || $today->gt($this->valid_until)) {
            return false;
        }

        // Check time restrictions if set
        if ($this->time_start && $this->time_end) {
            $now = Carbon::now()->format('H:i:s');
            if ($now < $this->time_start || $now > $this->time_end) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get a human-readable type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'combo' => 'Combo / Bundle',
            'discount_product' => 'Diskon Produk',
            'discount_category' => 'Diskon Kategori',
            default => $this->type,
        };
    }

    /**
     * Get a human-readable discount description.
     */
    public function getDiscountDescriptionAttribute(): string
    {
        if ($this->type === 'combo') {
            return 'Harga paket: ' . format_rupiah($this->combo_price);
        }

        if ($this->discount_type === 'percentage') {
            return 'Diskon ' . (int) $this->discount_value . '%';
        }

        return 'Diskon ' . format_rupiah($this->discount_value);
    }

    /**
     * Get time range description.
     */
    public function getTimeRangeAttribute(): string
    {
        if ($this->time_start && $this->time_end) {
            return substr($this->time_start, 0, 5) . ' - ' . substr($this->time_end, 0, 5);
        }
        return 'Sepanjang hari';
    }
}
