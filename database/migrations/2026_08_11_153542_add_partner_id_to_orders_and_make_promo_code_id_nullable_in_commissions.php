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
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'partner_id')) {
                    $table->foreignId('partner_id')->nullable()->constrained('users')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('partner_commissions')) {
            Schema::table('partner_commissions', function (Blueprint $table) {
                $table->foreignId('promo_code_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('partner_commissions')) {
            Schema::table('partner_commissions', function (Blueprint $table) {
                // To safely reverse change, we would set it back to not nullable,
                // but if there are null values it might fail, so we leave it as is or do a try/catch.
                try {
                    $table->foreignId('promo_code_id')->nullable(false)->change();
                } catch (\Exception $e) {
                    // Do nothing
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'partner_id')) {
                    $table->dropForeign(['partner_id']);
                    $table->dropColumn('partner_id');
                }
            });
        }
    }
};
