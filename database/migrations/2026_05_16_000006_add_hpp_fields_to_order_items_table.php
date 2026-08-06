<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('hpp_per_item', 12, 2)->default(0)->after('subtotal');
            $table->decimal('total_hpp', 12, 2)->default(0)->after('hpp_per_item');
            $table->decimal('gross_profit', 12, 2)->default(0)->after('total_hpp');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['hpp_per_item', 'total_hpp', 'gross_profit']);
        });
    }
};
