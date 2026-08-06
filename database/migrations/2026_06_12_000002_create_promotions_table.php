<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->enum('type', ['combo', 'discount_product', 'discount_category']);
            $table->enum('discount_type', ['percentage', 'fixed_price'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('combo_price', 12, 2)->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->date('valid_from');
            $table->date('valid_until');
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
