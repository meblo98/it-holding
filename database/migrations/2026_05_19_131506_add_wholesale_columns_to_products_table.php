<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('wholesale_qty')->default(5);
            $table->decimal('wholesale_discount_rate', 5, 2)->default(10.00);
            $table->decimal('wholesale_discount_limit', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['wholesale_qty', 'wholesale_discount_rate', 'wholesale_discount_limit']);
        });
    }
};
