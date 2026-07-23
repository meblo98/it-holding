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
        if (!Schema::hasTable('partner_promo_codes')) {
            Schema::create('partner_promo_codes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('partner_id')->constrained('users')->onDelete('cascade');
                $table->string('code')->unique();
                $table->decimal('discount_percent', 5, 2)->default(0.00);
                $table->decimal('commission_percent', 5, 2)->default(0.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'promo_code_id')) {
                    $table->foreignId('promo_code_id')->nullable()->constrained('partner_promo_codes')->nullOnDelete();
                }
                if (!Schema::hasColumn('orders', 'discount_amount')) {
                    $table->decimal('discount_amount', 15, 2)->default(0.00);
                }
            });
        }

        if (!Schema::hasTable('partner_commissions')) {
            Schema::create('partner_commissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('partner_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('promo_code_id')->constrained('partner_promo_codes')->onDelete('cascade');
                $table->decimal('order_amount', 15, 2);
                $table->decimal('commission_amount', 15, 2);
                $table->string('status')->default('pending'); // pending, paid, cancelled
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('partner_commissions')) {
            Schema::dropIfExists('partner_commissions');
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'promo_code_id')) {
                    $table->dropForeign(['promo_code_id']);
                    $table->dropColumn('promo_code_id');
                }
                if (Schema::hasColumn('orders', 'discount_amount')) {
                    $table->dropColumn('discount_amount');
                }
            });
        }

        if (Schema::hasTable('partner_promo_codes')) {
            Schema::dropIfExists('partner_promo_codes');
        }
    }
};
