<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_claims', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // REP-2026-0001
            $table->foreignId('care_subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('symptoms');
            $table->text('description');
            $table->date('started_at')->nullable();
            $table->string('drop_mode', 20); // depot | collecte
            $table->string('contact_phone', 30);
            $table->json('photos')->nullable();
            $table->string('video_path')->nullable();
            $table->string('status', 30)->default('received');
            $table->text('refusal_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('care_claim_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('care_claim_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30);
            $table->text('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_claim_events');
        Schema::dropIfExists('care_claims');
    }
};
