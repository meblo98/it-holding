<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Extends partner_prospects to also carry "opportunity" dossiers submitted by
     * apporteurs d'affaires (doc §4, §30-33): a one-off introduction rather than an
     * ongoing sales lead, with its own ID, anti-duplicate tracking and commission.
     */
    public function up(): void
    {
        Schema::table('partner_prospects', function (Blueprint $table) {
            $table->string('entry_type')->default('prospect')->after('partner_id'); // prospect, opportunity
            $table->string('opportunity_ref')->nullable()->unique()->after('entry_type'); // ITH-OPP-000154
            $table->foreignId('duplicate_of_id')->nullable()->after('opportunity_ref')
                ->constrained('partner_prospects')->nullOnDelete();
            $table->string('duplicate_status')->nullable()->after('duplicate_of_id'); // flagged, cleared
            $table->decimal('contract_amount', 15, 2)->nullable()->after('budget'); // montant réel une fois l'affaire conclue
            $table->string('commission_type')->nullable()->after('contract_amount'); // percent, fixed
            $table->decimal('commission_value', 15, 2)->nullable()->after('commission_type'); // % si percent, FCFA si fixed
            $table->decimal('commission_amount', 15, 2)->nullable()->after('commission_value'); // montant calculé
            $table->json('arbitration_history')->nullable()->after('commission_amount');

            $table->index('phone');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_prospects', function (Blueprint $table) {
            $table->dropForeign(['duplicate_of_id']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['email']);
            $table->dropColumn([
                'entry_type', 'opportunity_ref', 'duplicate_of_id', 'duplicate_status',
                'contract_amount', 'commission_type', 'commission_value', 'commission_amount',
                'arbitration_history',
            ]);
        });
    }
};
