<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'balance',
        'unique_token',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->unique_token)) {
                $customer->unique_token = Str::random(32);
            }
        });
    }

    public function mutations()
    {
        return $this->hasMany(CustomerMutation::class)->latest();
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->latest();
    }

    /**
     * Formats phone number to standard Indonesian international format (628xxx)
     */
    public static function formatPhoneNumber(?string $phone): ?string
    {
        if (!$phone) return null;

        // Clean non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '08')) {
            return '628' . substr($cleaned, 2);
        }

        if (str_starts_with($cleaned, '8')) {
            return '62' . $cleaned;
        }

        return $cleaned;
    }
}
