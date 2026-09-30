<?php

namespace App\Services;

use App\Models\Client;
use App\Models\MissionApplication;
use App\Models\PartnerCommission;
use App\Models\PartnerProspect;
use App\Models\ProWalletEntry;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Portefeuille professionnel (doc §37-38) : registre immuable dont les
 * soldes sont calculés. Toute sortie d'argent passe par ici, sous verrou,
 * pour qu'un professionnel ne puisse jamais retirer plus que son disponible.
 */
class ProWallet
{
    public const EARNING_SOURCES = ['commission_vente', 'commission_apporteur', 'mission'];
    public const PAID_OUT_SOURCES = ['shop_credit_transfer', 'legacy_shop_credit_commission', 'legacy_shop_credit_mission'];

    /**
     * Inscrit un gain net (après retenue éventuelle). L'unicité en base
     * (source_type, source_id) interdit de créditer deux fois la même source.
     */
    public function credit(User $user, string $sourceType, int $sourceId, float $gross, float $withholding, float $net, ?int $taxRuleId, string $description): ProWalletEntry
    {
        return ProWalletEntry::create([
            'user_id' => $user->id,
            'direction' => 'credit',
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'gross_amount' => $gross,
            'withholding_amount' => $withholding,
            'amount' => $net,
            'tax_rule_id' => $taxRuleId,
            'description' => $description,
        ]);
    }

    public function available(User $user): float
    {
        $totals = ProWalletEntry::where('user_id', $user->id)
            ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'credit' THEN amount ELSE -amount END), 0) AS balance")
            ->first();

        return round((float) $totals->balance, 2);
    }

    /**
     * Les six indicateurs du §38.
     */
    public function balances(User $user): array
    {
        $entries = ProWalletEntry::where('user_id', $user->id);

        $earned = (clone $entries)->where('direction', 'credit')->whereIn('source_type', self::EARNING_SOURCES)->sum('amount');
        $withheld = (clone $entries)->where('direction', 'credit')->whereIn('source_type', self::EARNING_SOURCES)->sum('withholding_amount');
        $paidOut = (clone $entries)->where('direction', 'debit')->whereIn('source_type', self::PAID_OUT_SOURCES)->sum('amount')
            + WithdrawalRequest::where('user_id', $user->id)->where('status', 'paid')->sum('amount');
        $inWithdrawal = WithdrawalRequest::where('user_id', $user->id)->whereIn('status', ['requested', 'approved'])->sum('amount');

        // En attente : commissions sur commandes non terminées, missions validées non encore payées (montants bruts).
        $pending = PartnerCommission::where('partner_id', $user->id)->where('status', 'pending')->sum('commission_amount')
            + MissionApplication::where('user_id', $user->id)->where('status', 'validated')->sum('agreed_amount');

        // Bloqué : gains confirmés mais retenus faute de contrat accepté (montants bruts).
        $blocked = PartnerCommission::where('partner_id', $user->id)->where('status', 'blocked_no_contract')->sum('commission_amount')
            + PartnerProspect::opportunities()->where('partner_id', $user->id)->where('status', 'won')->whereNull('credited_at')
                ->whereNotNull('commission_type')->get()->sum(fn ($o) => $o->computeCommission() ?? 0);

        return [
            'available' => $this->available($user),
            'pending' => round((float) $pending, 2),
            'blocked' => round((float) $blocked, 2),
            'earned' => round((float) $earned, 2),
            'paid_out' => round((float) $paidOut, 2),
            'withheld' => round((float) $withheld, 2),
            'in_withdrawal' => round((float) $inWithdrawal, 2),
        ];
    }

    /**
     * Demande de retrait : le montant est réservé (débité) immédiatement ;
     * un refus le recrédite par une ligne compensatoire.
     */
    public function requestWithdrawal(User $user, float $amount, string $method, string $accountDetails): WithdrawalRequest
    {
        return DB::transaction(function () use ($user, $amount, $method, $accountDetails) {
            $this->lock($user);
            $this->assertAvailable($user, $amount);

            $request = WithdrawalRequest::create([
                'reference' => WithdrawalRequest::generateReference(),
                'user_id' => $user->id,
                'amount' => $amount,
                'method' => $method,
                'account_details' => $accountDetails,
                'status' => 'requested',
            ]);

            $this->debit($user, 'withdrawal', $request->id, $amount, "Demande de retrait {$request->reference} ({$request->method_label})");

            return $request;
        });
    }

    public function rejectWithdrawal(WithdrawalRequest $request, User $admin, ?string $note): void
    {
        DB::transaction(function () use ($request, $admin, $note) {
            $request->update([
                'status' => 'rejected',
                'admin_note' => $note,
                'processed_by' => $admin->id,
                'processed_at' => now(),
            ]);

            ProWalletEntry::create([
                'user_id' => $request->user_id,
                'direction' => 'credit',
                'source_type' => 'withdrawal_refund',
                'source_id' => $request->id,
                'amount' => $request->amount,
                'description' => "Retrait {$request->reference} refusé — montant recrédité",
            ]);
        });
    }

    /**
     * Transfert instantané vers le crédit boutique (portefeuille client),
     * utilisable au paiement des commandes.
     */
    public function transferToShopCredit(User $user, float $amount): void
    {
        DB::transaction(function () use ($user, $amount) {
            $this->lock($user);
            $this->assertAvailable($user, $amount);

            $client = $this->clientFor($user);
            $client->increment('wallet_balance', $amount);
            $transaction = WalletTransaction::create([
                'client_id' => $client->id,
                'type' => 'deposit',
                'amount' => $amount,
                'description' => 'Transfert depuis le portefeuille professionnel',
                'transaction_date' => now(),
            ]);

            $this->debit($user, 'shop_credit_transfer', $transaction->id, $amount, 'Transfert en crédit boutique');
        });
    }

    private function debit(User $user, string $sourceType, int $sourceId, float $amount, string $description): void
    {
        ProWalletEntry::create([
            'user_id' => $user->id,
            'direction' => 'debit',
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'amount' => $amount,
            'description' => $description,
        ]);
    }

    /**
     * Verrou par utilisateur : deux retraits simultanés ne peuvent pas
     * consommer le même disponible.
     */
    private function lock(User $user): void
    {
        User::whereKey($user->id)->lockForUpdate()->first();
    }

    private function assertAvailable(User $user, float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Le montant doit être positif.');
        }
        if ($amount > $this->available($user)) {
            throw new InvalidArgumentException('Montant supérieur à votre solde disponible.');
        }
    }

    private function clientFor(User $user): Client
    {
        $client = Client::where('user_id', $user->id)->first();
        if ($client) {
            return $client;
        }

        $names = explode(' ', $user->name, 2);

        return Client::create([
            'user_id' => $user->id,
            'first_name' => $names[0] ?? 'Partner',
            'last_name' => $names[1] ?? 'Partner',
            'email' => $user->email,
            'phone' => $user->phone ?? '770000000',
            'wallet_balance' => 0,
            'current_balance' => 0,
        ]);
    }
}
