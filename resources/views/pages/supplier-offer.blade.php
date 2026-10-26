@extends('layouts.app')

@section('title', 'Demande d\'offre - ' . config('app.name'))

@section('content')
<section class="bg-gray-50 min-h-screen">
    <div class="max-w-lg mx-auto px-4 py-10">
        <p class="text-xs font-bold tracking-widest text-gold-600 uppercase">IT Holding Services</p>
        <h1 class="mt-1 text-2xl font-semibold text-navy-900">Demande d'offre</h1>
        <p class="text-sm text-gray-500">Bonjour {{ $offer->supplier->name }}. Référence : commande n° {{ $offer->request->order_id }}.</p>

        @foreach (['success', 'error'] as $flash)
            @if (session($flash))
                <div class="mt-4 rounded-lg border px-4 py-3 text-sm {{ $flash === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }}">{{ session($flash) }}</div>
            @endif
        @endforeach

        <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Articles demandés</h2>
            <ul class="divide-y divide-gray-100 text-sm">
                @foreach ($items as $item)
                    <li class="py-2 flex justify-between gap-3">
                        <span class="text-navy-900">{{ $item->product?->name ?? 'Produit' }}</span>
                        <span class="font-semibold">× {{ $item->quantity }}</span>
                    </li>
                @endforeach
            </ul>
            <p class="text-sm text-gray-600">Livraison : <strong>{{ $zone }}</strong>@if ($offer->distance_km !== null) · environ {{ number_format($offer->distance_km, 0, ',', ' ') }} km de chez vous @endif</p>
            <p class="text-xs text-gray-400">La livraison au client est assurée par nos livreurs : vous préparez la commande et la remettez à notre livreur.</p>
        </div>

        @if ($offer->status === 'selected')
            <div class="mt-6 rounded-lg border border-green-200 bg-green-50 text-green-800 p-5 text-sm">
                <strong>Votre offre a été retenue.</strong> Prix : {{ number_format($offer->price, 0, ',', ' ') }} FCFA · délai : {{ $offer->delay_hours }} h. Préparez la commande, un livreur viendra la récupérer.
            </div>
        @elseif (in_array($offer->status, ['rejected', 'expired'], true) || $offer->request->status !== 'open')
            <div class="mt-6 rounded-lg border border-gray-200 bg-white text-gray-600 p-5 text-sm">
                @if ($offer->status === 'rejected') Une autre offre a été retenue pour cette commande. Merci pour votre réponse.
                @else Cette demande est clôturée. @endif
            </div>
        @elseif ($open)
            <form method="POST" action="{{ route('supplier.offer.submit', $offer->token) }}" class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                @csrf
                <p class="text-sm text-gray-600">Réponse attendue avant <strong>{{ $offer->request->deadline_at->format('H:i') }}</strong>.
                    @if ($offer->status === 'offered') Votre offre actuelle : {{ number_format($offer->price, 0, ',', ' ') }} FCFA, {{ $offer->delay_hours }} h. Vous pouvez la modifier. @endif
                </p>

                <div>
                    <label class="block text-sm font-medium text-navy-900">Votre prix total (FCFA)</label>
                    <input type="number" name="price" min="1" required value="{{ old('price', $offer->price) }}" class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">
                    @error('price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-900">Délai avant remise au livreur (heures)</label>
                    <input type="number" name="delay_hours" min="1" max="720" required value="{{ old('delay_hours', $offer->delay_hours) }}" class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">
                    @error('delay_hours')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-900">Remarque (facultatif)</label>
                    <textarea name="note" rows="2" maxlength="300" class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">{{ old('note', $offer->note) }}</textarea>
                </div>

                <button type="submit" name="action" value="offer" class="btn-primary-gold w-full justify-center">Envoyer mon offre</button>
                <button type="submit" name="action" value="decline" formnovalidate class="w-full text-sm text-gray-500 underline">Je ne peux pas répondre</button>
            </form>
        @else
            <div class="mt-6 rounded-lg border border-gray-200 bg-white text-gray-600 p-5 text-sm">Cette demande est clôturée : le délai est dépassé.</div>
        @endif
    </div>
</section>
@endsection
