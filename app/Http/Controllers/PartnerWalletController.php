<?php

namespace App\Http\Controllers;

use App\Models\ProWalletEntry;
use App\Models\WithdrawalRequest;
use App\Services\ProWallet;
use App\Services\WithholdingCertificate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class PartnerWalletController extends Controller
{
    /**
     * Portefeuille professionnel (doc §38) : soldes, historique, retraits.
     */
    public function index(ProWallet $wallet)
    {
        $user = $this->partner();

        $balances = $wallet->balances($user);
        $entries = ProWalletEntry::where('user_id', $user->id)->latest()->latest('id')->paginate(15);
        $withdrawals = WithdrawalRequest::where('user_id', $user->id)->latest()->take(10)->get();

        return view('pages.shop.partner.wallet', compact('user', 'balances', 'entries', 'withdrawals'));
    }

    public function withdraw(Request $request, ProWallet $wallet)
    {
        $user = $this->partner();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:' . implode(',', array_keys(WithdrawalRequest::METHODS)),
            'account_details' => 'required|string|max:255',
        ], [
            'account_details.required' => 'Indiquez le numéro ou le compte sur lequel recevoir le versement.',
        ]);

        try {
            $withdrawal = $wallet->requestWithdrawal($user, (float) $validated['amount'], $validated['method'], $validated['account_details']);
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', "Demande de retrait {$withdrawal->reference} enregistrée. IT Holding vous informera dès le versement.");
    }

    public function transferToShopCredit(Request $request, ProWallet $wallet)
    {
        $user = $this->partner();

        $validated = $request->validate(['amount' => 'required|numeric|min:1']);

        try {
            $wallet->transferToShopCredit($user, (float) $validated['amount']);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', number_format($validated['amount'], 0, ',', ' ') . ' FCFA transférés en crédit boutique, utilisables pour vos achats.');
    }

    /**
     * Attestation de retenue à la source du professionnel (doc §36).
     */
    public function certificate(string $type, int $id)
    {
        $user = $this->partner();
        abort_unless(in_array($type, WithholdingCertificate::TYPES, true), 404);

        try {
            $cert = app(WithholdingCertificate::class)->resolve($type, $id);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        abort_unless($cert['beneficiary']->id === $user->id, 404);

        return view('admin.tax-rules.certificate', compact('cert'));
    }

    private function partner()
    {
        $user = Auth::user();
        abort_unless($user->isPartner(), 403, 'Accès réservé aux partenaires approuvés.');

        return $user;
    }
}
