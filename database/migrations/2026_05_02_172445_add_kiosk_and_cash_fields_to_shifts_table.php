<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->decimal('actual_closing_cash', 12, 2)->nullable()->after('closing_cash');
            $table->decimal('net_revenue', 12, 2)->nullable()->after('actual_closing_cash');
            $table->decimal('cash_left_for_next_shift', 12, 2)->nullable()->after('net_revenue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['actual_closing_cash', 'net_revenue', 'cash_left_for_next_shift']);
        });
    }
};
