<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_reports', function (Blueprint $table) {
            $table->foreignId('ingredient_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('source', 30)->default('manual')->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('stock_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ingredient_id');
            $table->dropColumn('source');
        });
    }
};
