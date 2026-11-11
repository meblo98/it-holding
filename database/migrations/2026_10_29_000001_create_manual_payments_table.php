<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_payments', function (Blueprint $table) {
            $table->id();
            $table->morphs('payable'); // commande ou abonnement Care
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method', 20)->default('wave');
            $table->unsignedInteger('amount');
            $table->string('reference', 80)->nullable();   // référence de transaction fournie par le client
            $table->string('proof_path')->nullable();      // capture d'écran (disque privé)
            // awaiting | submitted | confirmed | rejected
            $table->string('status', 20)->default('awaiting');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'submitted_at']);
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_payments');
    }
};
