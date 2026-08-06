<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_category_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('unit', 30);
            $table->decimal('current_qty', 12, 2)->default(0);
            $table->decimal('min_qty', 12, 2)->default(0);
            $table->decimal('cost_per_base_unit', 14, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
