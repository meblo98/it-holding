<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saving_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->decimal('target_amount', 15, 2);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->string('status')->default('active'); // 'active', 'completed', 'withdrawn'
            $table->timestamps();
        });

        Schema::create('saving_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saving_plan_id')->constrained('saving_plans')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('type'); // 'deposit', 'withdrawal'
            $table->string('payment_method'); // 'wallet', 'wave', 'orange_money', 'card', etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saving_transactions');
        Schema::dropIfExists('saving_plans');
    }
};
