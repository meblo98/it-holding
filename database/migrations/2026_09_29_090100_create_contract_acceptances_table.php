<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Preuve d'acceptation électronique (doc §22) : IP, user-agent, horodatage
     * et empreinte (hash) du contenu réellement affiché au moment du clic —
     * pas une signature électronique qualifiée, mais une trace horodatée
     * exploitable. Un point d'intégration pour une signature qualifiée reste
     * à prévoir pour les contrats qui l'exigeraient (référence doc §22).
     */
    public function up(): void
    {
        Schema::create('contract_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('document_hash'); // sha256 du contenu accepté à cet instant
            $table->timestamp('accepted_at');
            $table->timestamps();

            $table->unique(['contract_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_acceptances');
    }
};
