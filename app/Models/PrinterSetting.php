<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrinterSetting extends Model
{
    protected $fillable = [
        'name', 'type', 'address', 'paper_size',
        'receipt_header', 'receipt_footer',
        'show_logo', 'is_default',
    ];

    protected function casts(): array
    {
        return [
            'show_logo' => 'boolean',
            'is_default' => 'boolean',
        ];
    }
}
