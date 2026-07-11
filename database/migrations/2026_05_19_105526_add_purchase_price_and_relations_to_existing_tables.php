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
            $table->decimal('purchase_price', 15, 2)->default(0.00)->after('price');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('purchase_price', 15, 2)->default(0.00)->after('price');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('invoice_id')->constrained()->onDelete('set null');
            $table->decimal('purchase_price', 15, 2)->default(0.00)->after('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'purchase_price']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('purchase_price');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('purchase_price');
        });
    }
};
