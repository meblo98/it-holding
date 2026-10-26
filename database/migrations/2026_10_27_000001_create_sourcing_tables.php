<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sourcing_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            // open | awarded | failed | needs_location | cancelled
            $table->string('status', 20)->default('open');
            $table->timestamp('deadline_at')->nullable();
            $table->unsignedBigInteger('awarded_offer_id')->nullable();
            $table->timestamp('awarded_at')->nullable();
            $table->string('award_mode', 10)->nullable(); // auto | manual
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'deadline_at']);
        });

        Schema::create('supplier_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sourcing_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            // invited | offered | declined | selected | rejected | expired
            $table->string('status', 20)->default('invited');
            $table->unsignedInteger('price')->nullable();       // prix total proposé (FCFA)
            $table->unsignedInteger('delay_hours')->nullable(); // délai avant remise à notre livreur
            $table->text('note')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->decimal('score', 6, 2)->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['sourcing_request_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_offers');
        Schema::dropIfExists('sourcing_requests');
    }
};
