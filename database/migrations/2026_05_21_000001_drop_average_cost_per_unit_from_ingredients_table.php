<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('ingredients', 'average_cost_per_unit')) {
            return;
        }

        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn('average_cost_per_unit');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('ingredients', 'average_cost_per_unit')) {
            return;
        }

        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('average_cost_per_unit', 14, 4)->default(0)->after('min_qty');
        });
    }
};
