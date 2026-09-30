<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Badges délivrés aux professionnels (doc §11) : chaque délivrance a son
     * propre numéro vérifiable par QR code, une date de délivrance, une
     * expiration éventuelle et un statut (révocable).
     */
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // ITH-BDG-000001
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->date('issued_at');
            $table->date('expires_at')->nullable();
            $table->string('status')->default('active'); // active, revoked
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};
