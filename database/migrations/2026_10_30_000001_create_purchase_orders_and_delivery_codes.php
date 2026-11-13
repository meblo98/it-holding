<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // BC-2026-0001
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_offer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('supplier_token', 64)->unique(); // page du fournisseur
            $table->string('pickup_token', 64)->unique();   // contenu du QR code scanné par le livreur
            $table->string('pickup_code', 6);               // code à remettre au livreur
            $table->unsignedInteger('agreed_price');
            $table->unsignedInteger('delay_hours')->nullable();
            $table->string('status', 20)->default('issued'); // issued | handed_over | cancelled
            $table->timestamp('handed_over_at')->nullable();
            $table->unsignedTinyInteger('pickup_attempts')->default(0);
            $table->timestamps();
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('delivery_token', 64)->nullable()->unique(); // QR montré par le client
            $table->string('delivery_code', 6)->nullable();             // code donné par le client
            $table->unsignedTinyInteger('dropoff_attempts')->default(0);
            $table->string('pickup_method', 10)->nullable();            // qr | code | admin
            $table->string('dropoff_method', 10)->nullable();
            $table->unsignedInteger('courier_fee')->nullable();         // revenu du livreur (FCFA)
            $table->timestamp('fee_paid_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropUnique(['delivery_token']);
            $table->dropColumn(['delivery_token', 'delivery_code', 'dropoff_attempts', 'pickup_method', 'dropoff_method', 'courier_fee', 'fee_paid_at']);
        });

        Schema::dropIfExists('purchase_orders');
    }
};
