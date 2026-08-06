<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('system_qty', 12, 2);
            $table->decimal('actual_qty', 12, 2);
            $table->decimal('difference', 12, 2); // actual - system
            $table->string('adjustment_type'); // surplus, deficit, match
            $table->text('notes')->nullable();
            $table->date('opname_date');
            $table->timestamps();

            $table->index('opname_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
