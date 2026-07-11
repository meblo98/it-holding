<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // SAV-2026-0001

            // Client & product
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('warranty_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Technicien

            // Client snapshot
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();

            // Product snapshot
            $table->string('product_name')->nullable();
            $table->string('serial_number')->nullable();

            // Ticket details
            $table->string('title'); // Titre court
            $table->text('description'); // Description de la panne
            $table->string('status')->default('open');
            // open, diagnosed, in_progress, waiting_parts, resolved, closed, cancelled
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('type')->default('repair');
            // repair, installation, maintenance, advice, warranty_claim

            // Technician work
            $table->text('diagnosis')->nullable();          // Diagnostic
            $table->text('intervention_notes')->nullable(); // Notes d'intervention
            $table->text('parts_used')->nullable();         // Pièces utilisées
            $table->decimal('repair_cost', 12, 2)->nullable();
            $table->boolean('covered_by_warranty')->default(false);

            // Dates
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->date('scheduled_date')->nullable(); // Date intervention prévue

            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Ticket attachments (photos)
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('type')->default('image'); // image, document
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('tickets');
    }
};
