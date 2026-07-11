<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // CARE-2026-0001

            // Client & product
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('warranty_id')->nullable()->constrained()->onDelete('set null');
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->string('product_name');
            $table->string('serial_number')->nullable();

            // Plan details
            $table->string('plan')->default('standard');
            // standard, premium, enterprise
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_months')->default(12);
            $table->decimal('price', 12, 2);

            // Plan benefits (stored as JSON flags)
            $table->boolean('has_priority_support')->default(true);
            $table->boolean('has_repair_discount')->default(true);
            $table->integer('repair_discount_pct')->default(20); // % discount
            $table->boolean('has_parts_discount')->default(false);
            $table->integer('parts_discount_pct')->default(0);
            $table->boolean('has_home_service')->default(false); // Intervention à domicile

            // Status
            $table->string('status')->default('active');
            // active, expired, cancelled, suspended

            // Payment
            $table->string('payment_status')->default('pending'); // pending, paid
            $table->decimal('amount_paid', 12, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_subscriptions');
    }
};
