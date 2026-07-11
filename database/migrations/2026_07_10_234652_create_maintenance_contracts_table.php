<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // CONT-2026-0001

            // Client
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->string('client_address')->nullable();

            // Contract terms
            $table->string('type')->default('standard');
            // basic, standard, premium, custom
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('price', 12, 2); // Montant total du contrat
            $table->string('billing_period')->default('annual'); // monthly, quarterly, annual

            // SLA
            $table->integer('interventions_included')->default(0); // Nombre d'interventions incluses
            $table->integer('interventions_used')->default(0);
            $table->integer('response_time_hours')->default(24); // SLA délai de réponse
            $table->text('scope')->nullable(); // Périmètre couvert

            // Status
            $table->string('status')->default('active'); // draft, active, expired, cancelled, suspended

            // Payment
            $table->string('payment_status')->default('pending'); // pending, partial, paid
            $table->decimal('amount_paid', 12, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_contracts');
    }
};
