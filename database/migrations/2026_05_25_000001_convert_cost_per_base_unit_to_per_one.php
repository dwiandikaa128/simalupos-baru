<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convert cost_per_base_unit from "per 1000 unit" to "per 1 unit"
        // for ingredients that use ml or gram.
        // Example: Rp 25.000 per 1000 ml → Rp 25 per 1 ml
        DB::table('ingredients')
            ->whereIn('unit', ['ml', 'gram'])
            ->where('cost_per_base_unit', '>', 0)
            ->update([
                'cost_per_base_unit' => DB::raw('cost_per_base_unit / 1000'),
            ]);
    }

    public function down(): void
    {
        // Reverse: multiply back by 1000 for ml/gram ingredients
        DB::table('ingredients')
            ->whereIn('unit', ['ml', 'gram'])
            ->where('cost_per_base_unit', '>', 0)
            ->update([
                'cost_per_base_unit' => DB::raw('cost_per_base_unit * 1000'),
            ]);
    }
};
