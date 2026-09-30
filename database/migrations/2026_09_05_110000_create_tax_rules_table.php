<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * TAX ENGINE (doc §34-36) : la retenue à la source n'est jamais un taux figé
     * dans le code ("BRS = 5%"). Chaque règle est une ligne configurable par le
     * comptable/fiscaliste, avec ses propres conditions d'application.
     */
    public function up(): void
    {
        Schema::create('tax_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ex: "Retenue BRS - prestations de services"
            $table->string('country', 2)->default('SN');
            $table->string('beneficiary_type')->default('all'); // individual, company, all
            $table->string('prestation_nature')->default('all'); // commission_vente, commission_apporteur, mission_freelance, all
            $table->decimal('rate', 5, 2)->default(0.00); // en %
            $table->decimal('threshold_amount', 15, 2)->nullable(); // ex: 25000 FCFA — sous ce seuil, pas de retenue
            $table->boolean('is_exempt')->default(false); // règle d'exonération explicite (prioritaire sur le taux)
            $table->string('tax_regime')->nullable(); // information libre : régime réel, micro, etc.
            $table->boolean('active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->text('notes')->nullable(); // référence légale, justification
            $table->timestamps();

            $table->index(['country', 'beneficiary_type', 'prestation_nature', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rules');
    }
};
