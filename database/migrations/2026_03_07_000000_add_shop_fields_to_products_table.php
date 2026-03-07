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
            $table->decimal('promo_price', 10, 2)->nullable()->after('price');
            $table->boolean('blackfriday')->default(false)->after('promo_price');
            $table->string('category')->nullable()->after('blackfriday');
            $table->string('brand')->nullable()->after('category');
            $table->string('condition')->nullable()->after('brand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['promo_price', 'blackfriday', 'category', 'brand', 'condition']);
        });
    }
};
