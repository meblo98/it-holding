<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Contrats numériques (doc §21-22, §63-64) : le contrat en vigueur pour une
     * catégorie du réseau (partner_type) est versionné ici. Une fois qu'une
     * version a au moins une acceptation, elle n'est plus modifiable — toute
     * évolution des conditions passe par une nouvelle version, pour que la
     * preuve d'acceptation (contract_acceptances) reste fiable dans le temps.
     */
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // commercial, apporteur, revendeur, freelance, prestataire, createur, all
            $table->string('version'); // ex: 1.0
            $table->string('title');
            $table->longText('content');
            $table->string('status')->default('draft'); // draft, active, archived
            $table->date('effective_from')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
