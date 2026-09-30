<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Missions freelance / prestataires (doc §14-20).
     *
     * Une mission publiée reçoit des candidatures ; une fois l'équipe
     * sélectionnée, la mission devient l'espace projet (référence projet,
     * tâches, messagerie avec pièces jointes). La table "projects" existante
     * est celle du portfolio, d'où le choix de ne pas la réutiliser.
     */
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // ITH-MIS-000001
            $table->string('project_ref')->nullable()->unique(); // ITH-2026-00001, attribué au démarrage
            $table->string('title');
            $table->text('description');
            $table->string('city')->nullable();
            $table->string('location')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->string('duration')->nullable(); // ex: "3 jours"
            $table->json('required_skills')->nullable();
            $table->text('equipment')->nullable();
            $table->date('start_date')->nullable();
            $table->date('apply_until')->nullable();
            $table->unsignedSmallInteger('positions')->default(1);
            $table->string('status')->default('draft'); // draft, open, closed, in_progress, completed, cancelled
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('mission_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('proposal');
            $table->decimal('proposed_rate', 15, 2)->nullable();
            $table->string('proposed_delay')->nullable();
            $table->text('experience')->nullable();
            $table->text('references')->nullable();
            $table->string('document_path')->nullable(); // disque privé
            $table->string('document_name')->nullable();
            // applied, shortlisted, interview, selected, contracted, in_progress, completed, validated, paid, rejected
            $table->string('status')->default('applied');
            $table->json('status_history')->nullable();
            $table->text('admin_notes')->nullable();
            $table->decimal('agreed_amount', 15, 2)->nullable();
            $table->foreignId('tax_rule_id')->nullable()->constrained('tax_rules')->nullOnDelete();
            $table->decimal('withholding_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['mission_id', 'user_id']);
        });

        Schema::create('mission_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('todo'); // todo, in_progress, done
            $table->date('due_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('mission_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body')->nullable();
            $table->string('attachment_path')->nullable(); // disque privé
            $table->string('attachment_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_messages');
        Schema::dropIfExists('mission_tasks');
        Schema::dropIfExists('mission_applications');
        Schema::dropIfExists('missions');
    }
};
