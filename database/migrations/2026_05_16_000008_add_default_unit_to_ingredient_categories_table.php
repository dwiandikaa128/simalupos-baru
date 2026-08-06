<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredient_categories', function (Blueprint $table) {
            $table->string('default_unit', 30)->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('ingredient_categories', function (Blueprint $table) {
            $table->dropColumn('default_unit');
        });
    }
};
