<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company_name')->nullable();
            $table->string('rccm')->nullable();
            $table->string('ninea')->nullable();
            $table->string('sector')->nullable(); // Secteur d'activité

            // Contact
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('phone2')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Sénégal');

            // Professional credit & payment
            $table->boolean('is_professional')->default(false);
            $table->decimal('credit_limit', 15, 2)->default(0);       // Plafond crédit
            $table->decimal('current_balance', 15, 2)->default(0);    // Solde dû actuel
            $table->decimal('wallet_balance', 15, 2)->default(0);     // Portefeuille prépayé
            $table->string('payment_terms')->nullable();               // semaine, 15j, mois, trimestre

            // Link to user account (optional)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
