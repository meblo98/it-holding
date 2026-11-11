<?php

namespace App\Http\Controllers;

use App\Models\CareSubscription;
use App\Models\ManualPayment;
use App\Models\Order;
use App\Services\ManualPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Côté client : page « Payer avec Wave » puis déclaration de la référence de transaction. */
class ManualPaymentController extends Controller
{
    private function mine(int $id): ManualPayment
    {
        return ManualPayment::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
    }

    public function show(int $payment)
    {
        $payment = $this->mine($payment);

        return view('pages.wave-payment', [
            'payment' => $payment,
            'waveUrl' => ManualPaymentService::waveUrl($payment->amount),
        ]);
    }

    public function submit(Request $request, int $payment)
    {
        $payment = $this->mine($payment);
        abort_unless(in_array($payment->status, ['awaiting', 'rejected'], true), 403);

        $data = $request->validate([
            'reference' => ['required', 'string', 'min:6', 'max:60', 'regex:/^[A-Za-z0-9\-_]+$/'],
            'proof'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'reference.required' => 'Indiquez la référence de votre transaction Wave.',
            'reference.min'      => 'La référence semble trop courte.',
            'reference.regex'    => 'La référence ne doit contenir que des lettres, chiffres et tirets.',
            'proof.image'        => 'La capture doit être une image.',
            'proof.max'          => 'La capture ne doit pas dépasser 4 Mo.',
        ]);

        if (ManualPaymentService::referenceAlreadyUsed($data['reference'], $payment->id)) {
            return back()->withInput()->withErrors(['reference' => 'Cette référence a déjà été utilisée pour un autre paiement.']);
        }

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $proofPath = Storage::disk('local')->putFileAs('wave-proofs/' . $payment->id, $file, Str::uuid() . '.' . $file->extension());
        }

        ManualPaymentService::submit($payment, $data['reference'], $proofPath);

        $payable = $payment->payable;
        $message = 'Merci ! Votre paiement Wave est en cours de vérification. Vous serez servi dès sa confirmation.';

        if ($payable instanceof Order) {
            return redirect()->route('shop.thanks', $payable->id)->with('success', $message);
        }

        return redirect()->route('dashboard.care')->with('success', $message);
    }
}
