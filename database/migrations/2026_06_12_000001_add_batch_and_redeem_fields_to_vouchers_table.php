<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('batch_group', 100)->nullable()->after('valid_until');
            $table->timestamp('redeemed_at')->nullable()->after('batch_group');
            $table->foreignId('redeemed_by_order_id')->nullable()->after('redeemed_at')
                  ->constrained('orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['redeemed_by_order_id']);
            $table->dropColumn(['batch_group', 'redeemed_at', 'redeemed_by_order_id']);
        });
    }
};
