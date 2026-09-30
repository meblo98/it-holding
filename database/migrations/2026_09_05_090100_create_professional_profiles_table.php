<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('professional_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('pro_id')->nullable()->unique(); // ITH-PRO-000125
            $table->string('city')->nullable();
            $table->json('skills')->nullable();
            $table->json('languages')->nullable();
            $table->string('availability')->nullable(); // available, busy, unavailable
            $table->unsignedTinyInteger('verification_level')->default(1); // 1 to 5, see doc §13
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_profiles');
    }
};
