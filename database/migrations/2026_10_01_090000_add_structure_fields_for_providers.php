<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prestataires / experts (doc §7) : un prestataire peut être une personne
     * ou une structure (entreprise de câblage, agence, cabinet…). Les
     * missions peuvent cibler les freelances, les prestataires, ou les deux.
     */
    public function up(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('ninea'); // raison sociale
            $table->string('rccm')->nullable()->after('company_name');
            $table->string('legal_representative')->nullable()->after('rccm');
            $table->unsignedSmallInteger('team_size')->nullable()->after('legal_representative');
            $table->string('website')->nullable()->after('team_size');
        });

        Schema::table('missions', function (Blueprint $table) {
            $table->json('target_types')->nullable()->after('positions'); // null = freelances et prestataires
        });
    }

    public function down(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'rccm', 'legal_representative', 'team_size', 'website']);
        });

        Schema::table('missions', function (Blueprint $table) {
            $table->dropColumn('target_types');
        });
    }
};
