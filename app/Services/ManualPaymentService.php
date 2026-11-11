<?php

namespace App\Services;

use App\Models\CareSubscription;
use App\Models\ManualPayment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Paiement par lien Wave : le client paie sur Wave, indique sa référence de transaction,
 * puis l'équipe vérifie dans Wave et confirme. L'activation n'a lieu qu'à la confirmation.
 */
class ManualPaymentService
{
    /** Lien Wave avec le montant déjà rempli. */
    public static function waveUrl(int $amount): string
    {
        $link = (string) config('payments.wave.link');

        return $link . (str_contains($link, '?') ? '&' : '?') . 'amount=' . $amount;
    }

    public static function start(Model $payable, int $amount, ?int $userId): ManualPayment
    {
        return ManualPayment::create([
            'payable_type' => $payable->getMorphClass(),
            'payable_id'   => $payable->getKey(),
            'user_id'      => $userId,
            'method'       => 'wave',
            'amount'       => $amount,
            'status'       => 'awaiting',
        ]);
    }

    /** Le client déclare avoir payé. */
    public static function submit(ManualPayment $payment, string $reference, ?string $proofPath): void
    {
        $payment->update([
            'reference'        => strtoupper(trim($reference)),
            'proof_path'       => $proofPath ?? $payment->proof_path,
            'status'           => 'submitted',
            'submitted_at'     => now(),
            'rejection_reason' => null,
        ]);

        try {
            WhatsAppService::sendToAdmin('💳 Paiement Wave à vérifier : ' . $payment->payable_label
                . ' · ' . number_format($payment->amount, 0, ',', ' ') . ' FCFA · réf. ' . $payment->reference
                . ' → Admin > Paiements Wave.');
        } catch (\Throwable $e) {
            Log::warning('Wave payment admin alert failed: ' . $e->getMessage());
        }
    }

    public static function referenceAlreadyUsed(string $reference, int $exceptId): bool
    {
        return ManualPayment::where('reference', strtoupper(trim($reference)))
            ->whereIn('status', ['submitted', 'confirmed'])
            ->where('id', '!=', $exceptId)
            ->exists();
    }

    /** L'admin a vérifié le paiement dans Wave : on active la commande ou l'abonnement. */
    public static function confirm(ManualPayment $payment, int $adminId): void
    {
        DB::transaction(function () use ($payment, $adminId) {
            $payment = ManualPayment::lockForUpdate()->findOrFail($payment->id);
            abort_unless($payment->status === 'submitted', 422, 'Ce paiement n\'est pas en attente de vérification.');

            $payable = $payment->payable;

            if ($payable instanceof Order) {
                if ($payable->payment_status !== 'paid') {
                    $payable->update(['payment_status' => 'paid', 'status' => 'processing']);
                }
            } elseif ($payable instanceof CareSubscription) {
                if ($payable->payment_status !== 'paid') {
                    $months = (int) ($payable->duration_months ?: 12);
                    $payable->update([
                        'payment_status' => 'paid',
                        'status'         => 'active',
                        'amount_paid'    => $payment->amount,
                        'paid_at'        => now(),
                        'start_date'     => today(),
                        'end_date'       => today()->addMonths($months),
                    ]);
                }
            }

            $payment->update(['status' => 'confirmed', 'reviewed_by' => $adminId, 'reviewed_at' => now(), 'rejection_reason' => null]);
        });
    }

    public static function reject(ManualPayment $payment, int $adminId, string $reason): void
    {
        abort_unless($payment->status === 'submitted', 422);

        $payment->update([
            'status'           => 'rejected',
            'reviewed_by'      => $adminId,
            'reviewed_at'      => now(),
            'rejection_reason' => $reason,
        ]);
    }
}
