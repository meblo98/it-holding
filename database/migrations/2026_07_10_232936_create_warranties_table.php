<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('warranties')) {
            return;
        }

        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // GAR-2026-0001

            // Linked entities
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');

            // Product info (snapshot in case product changes)
            $table->string('product_name');
            $table->string('serial_number')->nullable();
            $table->string('client_name');   // Snapshot
            $table->string('client_phone')->nullable();

            // Warranty details
            $table->date('purchase_date');
            $table->date('expiry_date');
            $table->integer('duration_months')->default(12);
            $table->string('type')->default('standard'); // standard, extended, care_plus
            $table->string('status')->default('active'); // active, expired, void, claimed

            // Coverage
            $table->text('coverage_notes')->nullable();   // Ce qui est couvert
            $table->text('exclusions')->nullable();       // Exclusions

            // Internal notes
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
