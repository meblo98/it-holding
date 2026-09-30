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
        Schema::table('partner_commissions', function (Blueprint $table) {
            $table->foreignId('tax_rule_id')->nullable()->after('commission_amount')
                ->constrained('tax_rules')->nullOnDelete();
            $table->decimal('withholding_amount', 15, 2)->default(0)->after('tax_rule_id');
            $table->decimal('net_amount', 15, 2)->nullable()->after('withholding_amount');
        });

        Schema::table('partner_prospects', function (Blueprint $table) {
            $table->foreignId('tax_rule_id')->nullable()->after('commission_amount')
                ->constrained('tax_rules')->nullOnDelete();
            $table->decimal('withholding_amount', 15, 2)->default(0)->after('tax_rule_id');
            $table->decimal('net_amount', 15, 2)->nullable()->after('withholding_amount');
        });

        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->string('beneficiary_type')->default('individual')->after('verification_level'); // individual, company
            $table->string('ninea')->nullable()->after('beneficiary_type'); // doc §36 : mention requise sur les factures des prestataires
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_commissions', function (Blueprint $table) {
            $table->dropForeign(['tax_rule_id']);
            $table->dropColumn(['tax_rule_id', 'withholding_amount', 'net_amount']);
        });

        Schema::table('partner_prospects', function (Blueprint $table) {
            $table->dropForeign(['tax_rule_id']);
            $table->dropColumn(['tax_rule_id', 'withholding_amount', 'net_amount']);
        });

        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropColumn(['beneficiary_type', 'ninea']);
        });
    }
};
