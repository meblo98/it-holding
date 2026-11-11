@extends('layouts.app')

@section('title', 'Paiement Wave - ' . config('app.name'))

@section('content')
@php
    $fmt = fn ($n) => number_format($n, 0, ',', ' ') . ' FCFA';
    $logo = file_exists(public_path('images/wave.png')) ? asset('images/wave.png') : null;
    $submitted = $payment->status === 'submitted';
    $confirmed = $payment->status === 'confirmed';
@endphp
<section class="bg-gray-50 min-h-screen">
    <div class="max-w-lg mx-auto px-4 py-10">
        <p class="text-xs font-bold tracking-widest text-gold-600 uppercase">IT Holding Services</p>
        <h1 class="mt-1 text-2xl font-semibold text-navy-900">Paiement par Wave</h1>
        <p class="text-sm text-gray-500">{{ $payment->payable_label }}</p>

        <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-6 text-center">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Montant à payer</p>
            <p class="mt-1 text-4xl font-black text-navy-900">{{ $fmt($payment->amount) }}</p>
        </div>

        @if ($confirmed)
            <div class="mt-6 rounded-lg border border-green-200 bg-green-50 text-green-800 p-5 text-sm">Paiement confirmé. Merci !</div>
        @elseif ($submitted)
            <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 text-blue-800 p-5 text-sm">
                Votre paiement (réf. <strong>{{ $payment->reference }}</strong>) est en cours de vérification. Vous serez prévenu dès sa confirmation.
            </div>
        @else
            @if ($payment->status === 'rejected')
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 text-red-700 p-5 text-sm">
                    Votre précédente déclaration a été refusée : {{ $payment->rejection_reason }}. Vous pouvez la corriger ci-dessous.
                </div>
            @endif

            <div class="mt-6 space-y-3">
                <p class="text-sm font-semibold text-navy-900">1. Payez avec Wave</p>
                <a href="{{ $waveUrl }}" target="_blank" rel="noopener" class="w-full flex items-center justify-center gap-3 rounded-lg px-4 py-4 font-semibold text-white" style="background:#1DC8FF">
                    @if ($logo)<img src="{{ $logo }}" alt="Wave" class="h-7">@else<span class="text-xl font-black">wave</span>@endif
                    Ouvrir Wave et payer {{ $fmt($payment->amount) }}
                </a>
                <p class="text-xs text-gray-500">Si le montant n'est pas déjà rempli dans Wave, saisissez exactement <strong>{{ $fmt($payment->amount) }}</strong>.</p>
            </div>

            <form method="POST" action="{{ route('wave.payment.submit', $payment->id) }}" enctype="multipart/form-data" class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                @csrf
                <p class="text-sm font-semibold text-navy-900">2. Confirmez votre paiement</p>
                <div>
                    <label class="block text-sm font-medium text-navy-900">Référence de la transaction Wave</label>
                    <input name="reference" value="{{ old('reference') }}" required maxlength="60" placeholder="Ex : T_ABC123XYZ" class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">
                    <p class="mt-1 text-xs text-gray-400">Visible dans le message de confirmation Wave ou dans votre historique.</p>
                    @error('reference')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-900">Capture d'écran (facultatif)</label>
                    <input type="file" name="proof" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm">
                    @error('proof')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-primary-gold w-full justify-center">J'ai payé, envoyer pour vérification</button>
            </form>
        @endif
    </div>
</section>
@endsection
