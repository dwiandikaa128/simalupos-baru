<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_expenses', function (Blueprint $table) {
            $table->foreignId('ingredient_id')->nullable()->after('amount')->constrained()->nullOnDelete();
            $table->decimal('purchase_qty', 10, 2)->nullable()->after('ingredient_id');
            $table->string('purchase_unit', 20)->nullable()->after('purchase_qty');
            $table->text('notes')->nullable()->after('purchase_unit');
        });
    }

    public function down(): void
    {
        Schema::table('cash_expenses', function (Blueprint $table) {
            $table->dropForeign(['ingredient_id']);
            $table->dropColumn(['ingredient_id', 'purchase_qty', 'purchase_unit', 'notes']);
        });
    }
};
