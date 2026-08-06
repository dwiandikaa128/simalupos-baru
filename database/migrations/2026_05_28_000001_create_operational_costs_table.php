<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_costs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Listrik PLN", "Air PDAM"
            $table->string('category'); // utilities, rent, maintenance, supplies, other
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('period_month', 7); // format: 2026-05
            $table->boolean('is_recurring')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('period_month');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_costs');
    }
};
