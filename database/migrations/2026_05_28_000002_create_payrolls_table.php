<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('period_month', 7); // format: 2026-05
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('allowance', 12, 2)->default(0); // tunjangan (makan, transport, dll)
            $table->decimal('deduction', 12, 2)->default(0); // potongan
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('total_salary', 12, 2)->default(0);
            $table->integer('days_present')->default(0); // auto from attendance
            $table->integer('total_working_days')->default(0);
            $table->string('payment_status')->default('pending'); // pending, paid
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'period_month']);
            $table->index('period_month');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
