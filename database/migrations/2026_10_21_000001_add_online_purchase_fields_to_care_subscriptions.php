<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('care_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('care_subscriptions', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('care_subscriptions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('care_subscriptions', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('amount_paid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('care_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('care_subscriptions', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
            foreach (['payment_method', 'paid_at'] as $col) {
                if (Schema::hasColumn('care_subscriptions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
