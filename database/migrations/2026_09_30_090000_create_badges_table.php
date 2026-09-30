<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catalogue des badges du réseau pro (doc §10-11).
     */
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('icon', 16)->nullable();
            $table->string('color')->default('gray'); // green, blue, purple, orange, yellow, amber, red
            $table->unsignedTinyInteger('level')->default(1);
            $table->unsignedSmallInteger('validity_months')->nullable(); // null = sans expiration
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('badges')->insert([
            ['code' => 'profile_verified',      'name' => 'Profil vérifié',         'description' => 'Identité vérifiée.',                                        'icon' => '🟢', 'color' => 'green',  'level' => 1, 'validity_months' => null, 'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'professional_verified', 'name' => 'Professionnel vérifié',  'description' => 'Profil professionnel validé.',                              'icon' => '🔵', 'color' => 'blue',   'level' => 2, 'validity_months' => 24,   'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'official_partner',      'name' => 'Partenaire IT Holding',  'description' => 'Partenaire officiel.',                                      'icon' => '🟣', 'color' => 'purple', 'level' => 2, 'validity_months' => 12,   'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'freelance_verified',    'name' => 'Freelance vérifié',      'description' => 'Compétences et identité vérifiées.',                        'icon' => '🟠', 'color' => 'orange', 'level' => 3, 'validity_months' => 24,   'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'approved_provider',     'name' => 'Prestataire agréé',      'description' => 'Prestataire ayant satisfait les critères IT Holding.',      'icon' => '🟡', 'color' => 'yellow', 'level' => 3, 'validity_months' => 12,   'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'expert',                'name' => 'Expert',                 'description' => "Niveau d'expérience élevé.",                                'icon' => '⭐', 'color' => 'amber',  'level' => 4, 'validity_months' => null, 'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'certified',             'name' => 'Certifié IT Holding',    'description' => 'Formation / certification IT Holding obtenue.',             'icon' => '🛡️', 'color' => 'red',    'level' => 5, 'validity_months' => 24,   'active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
