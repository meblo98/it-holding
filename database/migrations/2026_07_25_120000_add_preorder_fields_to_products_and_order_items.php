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
            $table->date('available_at')->nullable()->after('stock');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('is_preorder')->default(false)->after('options');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('available_at');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('is_preorder');
        });
    }
};
