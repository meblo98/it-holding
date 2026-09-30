<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CRM global (doc §43) : un pipeline commercial central pour les
     * commerciaux internes, alimenté par le site, les partenaires et les
     * apporteurs, relié aux clients et aux devis existants.
     */
    public function up(): void
    {
        Schema::create('crm_deals', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // ITH-CRM-000001
            $table->string('title');
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('company')->nullable();
            $table->text('need')->nullable();
            $table->string('source')->default('commercial'); // website, partner, apporteur, commercial, referral, event, other
            $table->foreignId('partner_id')->nullable()->constrained('users')->nullOnDelete(); // partenaire / apporteur à l'origine
            $table->foreignId('partner_prospect_id')->nullable()->unique()->constrained('partner_prospects')->nullOnDelete();
            // new, contacted, qualified, quote, negotiation, won, delivery, loyalty, lost
            $table->string('stage')->default('new');
            $table->decimal('amount', 15, 2)->nullable();
            $table->foreignId('quote_id')->nullable()->constrained('quotes')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete(); // commercial en charge
            $table->dateTime('next_action_at')->nullable();
            $table->string('next_action_note')->nullable();
            $table->string('lost_reason')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['stage', 'owner_id']);
            $table->index('next_action_at');
        });

        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_deal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // note, call, email, whatsapp, meeting, stage_change, ai, quote
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('crm_deals');
    }
};
