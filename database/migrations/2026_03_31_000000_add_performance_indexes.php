<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('slug');
            $table->index('active');
            $table->index('category_id');
            $table->index('brand_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->index('slug');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['active']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['brand_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['slug']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['slug']);
        });
    }
};
