<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('ingredients', 'cost_per_base_unit')) {
            return;
        }

        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('cost_per_base_unit', 14, 4)->default(0)->after('min_qty');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('ingredients', 'cost_per_base_unit')) {
            return;
        }

        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn('cost_per_base_unit');
        });
    }
};
