<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Portefeuille professionnel unifié (doc §37-38).
     *
     * Registre immuable : chaque gain (commission vente, commission apporteur,
     * mission) et chaque sortie (retrait, transfert en crédit boutique) est une
     * ligne ; les soldes sont calculés. L'unicité (source_type, source_id)
     * empêche de créditer deux fois la même source.
     */
    public function up(): void
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // ITH-RET-000001
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('method'); // wave, orange_money, free_money, bank_transfer
            $table->string('account_details'); // numéro mobile money ou RIB/IBAN
            $table->string('status')->default('requested'); // requested, approved, paid, rejected
            $table->string('payment_reference')->nullable(); // n° de transaction du versement
            $table->text('admin_note')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('pro_wallet_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('direction'); // credit, debit
            // commission_vente, commission_apporteur, mission, withdrawal, withdrawal_refund,
            // shop_credit_transfer, legacy_shop_credit_commission, legacy_shop_credit_mission
            $table->string('source_type');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->decimal('withholding_amount', 15, 2)->default(0);
            $table->decimal('amount', 15, 2); // net, toujours positif ; le sens est donné par direction
            $table->foreignId('tax_rule_id')->nullable()->constrained('tax_rules')->nullOnDelete();
            $table->string('description');
            $table->timestamps();

            $table->unique(['source_type', 'source_id']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::table('partner_prospects', function (Blueprint $table) {
            $table->timestamp('credited_at')->nullable()->after('net_amount');
        });

        $this->backfillLegacyEarnings();
    }

    /**
     * Les gains déjà versés avant ce portefeuille l'ont été dans le crédit
     * boutique (clients.wallet_balance). On les inscrit au registre comme
     * « gagné » puis « déjà versé en crédit boutique » : l'historique et les
     * totaux sont justes, sans créditer deux fois.
     */
    private function backfillLegacyEarnings(): void
    {
        $now = now();

        $commissions = DB::table('partner_commissions')->where('status', 'paid')->get();
        foreach ($commissions as $c) {
            $net = $c->net_amount ?? $c->commission_amount;
            DB::table('pro_wallet_entries')->insert([
                [
                    'user_id' => $c->partner_id, 'direction' => 'credit', 'source_type' => 'commission_vente', 'source_id' => $c->id,
                    'gross_amount' => $c->commission_amount, 'withholding_amount' => $c->withholding_amount ?? 0, 'amount' => $net,
                    'tax_rule_id' => $c->tax_rule_id, 'description' => "Commission de la commande #{$c->order_id}",
                    'created_at' => $c->updated_at ?? $now, 'updated_at' => $now,
                ],
                [
                    'user_id' => $c->partner_id, 'direction' => 'debit', 'source_type' => 'legacy_shop_credit_commission', 'source_id' => $c->id,
                    'gross_amount' => 0, 'withholding_amount' => 0, 'amount' => $net, 'tax_rule_id' => null,
                    'description' => 'Déjà versé en crédit boutique (avant le portefeuille pro)',
                    'created_at' => $c->updated_at ?? $now, 'updated_at' => $now,
                ],
            ]);
        }

        $missions = DB::table('mission_applications')->where('status', 'paid')->get();
        foreach ($missions as $m) {
            DB::table('pro_wallet_entries')->insert([
                [
                    'user_id' => $m->user_id, 'direction' => 'credit', 'source_type' => 'mission', 'source_id' => $m->id,
                    'gross_amount' => $m->agreed_amount, 'withholding_amount' => $m->withholding_amount ?? 0, 'amount' => $m->net_amount ?? $m->agreed_amount,
                    'tax_rule_id' => $m->tax_rule_id, 'description' => "Mission (candidature #{$m->id})",
                    'created_at' => $m->paid_at ?? $now, 'updated_at' => $now,
                ],
                [
                    'user_id' => $m->user_id, 'direction' => 'debit', 'source_type' => 'legacy_shop_credit_mission', 'source_id' => $m->id,
                    'gross_amount' => 0, 'withholding_amount' => 0, 'amount' => $m->net_amount ?? $m->agreed_amount, 'tax_rule_id' => null,
                    'description' => 'Déjà versé en crédit boutique (avant le portefeuille pro)',
                    'created_at' => $m->paid_at ?? $now, 'updated_at' => $now,
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('partner_prospects', function (Blueprint $table) {
            $table->dropColumn('credited_at');
        });
        Schema::dropIfExists('pro_wallet_entries');
        Schema::dropIfExists('withdrawal_requests');
    }
};
