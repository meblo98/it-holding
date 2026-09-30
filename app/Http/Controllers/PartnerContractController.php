<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractAcceptance;
use App\Services\CommissionPayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerContractController extends Controller
{
    /**
     * Display the contract currently in force for the partner's category,
     * with its acceptance status (doc §21-22).
     */
    public function show()
    {
        $user = Auth::user();
        if (!$user->isPartner() && !$user->isPartnerPending()) {
            abort(403);
        }

        $contract = Contract::currentFor($user->partner_type ?? 'all');
        $accepted = $contract
            ? $user->contractAcceptances()->where('contract_id', $contract->id)->first()
            : null;

        return view('pages.shop.partner.contract', compact('user', 'contract', 'accepted'));
    }

    /**
     * Record the electronic acceptance: "J'ai lu et j'accepte le contrat."
     * Stores IP, user-agent, timestamp and a hash of the exact content shown
     * (doc §22) — not a qualified electronic signature, a timestamped proof
     * of consent that some contract types may later need to complement with
     * a dedicated e-signature integration.
     */
    public function accept(Request $request, Contract $contract)
    {
        $user = Auth::user();
        abort_unless($contract->status === 'active', 404);

        ContractAcceptance::updateOrCreate(
            ['contract_id' => $contract->id, 'user_id' => $user->id],
            [
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'document_hash' => $contract->contentHash(),
                'accepted_at' => now(),
            ]
        );

        // Les commissions bloquées faute de contrat sont versées dès maintenant.
        $released = app(CommissionPayout::class)->releaseBlockedFor($user->fresh());

        $message = 'Contrat accepté. Merci !';
        if ($released > 0) {
            $message .= " {$released} commission(s) en attente de votre contrat ont été versées sur votre portefeuille.";
        }

        return redirect()->route('dashboard.partner.contract')->with('success', $message);
    }
}
