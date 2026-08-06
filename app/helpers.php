<?php

if (! function_exists('format_qty')) {
    function format_qty(mixed $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',');
    }
}

if (! function_exists('format_rupiah')) {
    function format_rupiah(mixed $value): string
    {
        $amount = (float) $value;
        $prefix = $amount < 0 ? '-Rp ' : 'Rp ';
        $abs = abs($amount);

        // Show decimals for sub-rupiah values (e.g. cost per gram/ml)
        if ($abs > 0 && $abs < 1) {
            return $prefix . rtrim(rtrim(number_format($abs, 4, ',', '.'), '0'), ',');
        }

        // Show decimals if present
        if (fmod($abs, 1) > 0.0001) {
            return $prefix . rtrim(rtrim(number_format($abs, 2, ',', '.'), '0'), ',');
        }

        return $prefix . number_format($abs, 0, ',', '.');
    }
}
