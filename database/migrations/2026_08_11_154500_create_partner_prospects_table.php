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
        if (!Schema::hasTable('partner_prospects')) {
            Schema::create('partner_prospects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('partner_id')->constrained('users')->onDelete('cascade');
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('company')->nullable();
                $table->text('need')->nullable();
                $table->decimal('budget', 15, 2)->nullable();
                $table->string('status')->default('new'); // new, contacted, interested, proposal_sent, negotiating, won, lost
                $table->text('notes')->nullable();
                $table->dateTime('next_action_at')->nullable();
                $table->string('next_action_description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_prospects');
    }
};
