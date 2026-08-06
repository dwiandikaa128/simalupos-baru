<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    public static function get(string $key, $default = null): ?string
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, string $value, string $group = 'general'): self
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    public static function getByGroup(string $group): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('group', $group)->get();
    }

    public static function getTaxRate(): float
    {
        if (self::get('tax_enabled', 'false') === 'false') {
            return 0;
        }
        return (float) self::get('tax_rate', 11);
    }

    public static function getServiceChargeRate(): float
    {
        if (self::get('service_charge_enabled', 'false') === 'false') {
            return 0;
        }
        return (float) self::get('service_charge_rate', 5);
    }
}
