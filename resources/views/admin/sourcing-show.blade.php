@extends('layouts.admin')
@section('title', 'Appel d\'offres #' . $req->order_id)
@section('content')
@php $order = $req->order; $w = config('delivery.weights'); @endphp

<div class="mb-6">
    <a href="{{ route('admin.sourcing.index') }}" class="text-sm text-gray-400 hover:text-gray-700">← Appels d'offres</a>
    <div class="mt-1 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Commande #{{ $order->id }}</h1>
        <span class="px-3 py-1 rounded-full text-sm font-bold bg-blue-50 text-blue-700 border border-blue-200">{{ $req->status_label }}</span>
    </div>
    <p class="text-sm text-gray-500">{{ $order->customer_name }} · {{ $order->customer_address }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Articles</h2>
            <ul class="divide-y divide-gray-100 text-sm">
                @foreach($order->items as $item)
                    <li class="py-2 flex justify-between"><span>{{ $item->product?->name ?? 'Produit' }}</span><span class="font-bold">× {{ $item->quantity }}</span></li>
                @endforeach
            </ul>
            <p class="mt-3 text-xs text-gray-400">
                Position : {{ $order->delivery_lat !== null ? number_format($order->delivery_lat, 5) . ', ' . number_format($order->delivery_lng, 5) : 'inconnue' }}
                @if($order->delivery_source) ({{ ['gps' => 'GPS du client', 'city' => 'ville approximative', 'admin' => 'saisie admin'][$order->delivery_source] ?? $order->delivery_source }}) @endif
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
            <div class="px-6 pt-5">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Offres</h2>
                <p class="text-xs text-gray-400 mt-1">Score sur 100 : prix {{ $w['price'] * 100 }} %, délai {{ $w['delay'] * 100 }} %, proximité {{ $w['proximity'] * 100 }} %, fiabilité {{ $w['reliability'] * 100 }} %.</p>
            </div>
            <table class="w-full text-left text-sm mt-3">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400">
                    <tr><th class="px-4 py-2">Fournisseur</th><th class="px-4 py-2">Statut</th><th class="px-4 py-2">Prix</th><th class="px-4 py-2">Délai</th><th class="px-4 py-2">Distance</th><th class="px-4 py-2">Score</th><th class="px-4 py-2"></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($req->offers->sortByDesc('score') as $offer)
                        <tr class="{{ $offer->status === 'selected' ? 'bg-green-50' : '' }}">
                            <td class="px-4 py-2 font-bold">{{ $offer->supplier->name }}<span class="block text-xs font-normal text-gray-400">fiabilité {{ $offer->supplier->rating }}/5</span></td>
                            <td class="px-4 py-2">{{ $offer->status_label }}</td>
                            <td class="px-4 py-2">{{ $offer->price ? number_format($offer->price, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                            <td class="px-4 py-2">{{ $offer->delay_hours ? $offer->delay_hours . ' h' : '—' }}</td>
                            <td class="px-4 py-2">{{ $offer->distance_km !== null ? number_format($offer->distance_km, 1) . ' km' : '—' }}</td>
                            <td class="px-4 py-2 font-bold">{{ $offer->score ?? '—' }}</td>
                            <td class="px-4 py-2 text-right">
                                @if($req->status === 'open' && $offer->status === 'offered')
                                    <form method="POST" action="{{ route('admin.sourcing.manual', $req->id) }}" onsubmit="return confirm('Retenir cette offre à la place du choix automatique ?')">
                                        @csrf<input type="hidden" name="offer_id" value="{{ $offer->id }}">
                                        <button class="text-xs font-bold text-navy-700 underline">Retenir</button>
                                    </form>
                                @endif
                                @if($offer->status === 'invited')
                                    <input readonly value="{{ route('supplier.offer.show', $offer->token) }}" onclick="this.select()" class="w-32 text-xs rounded border border-gray-200 px-2 py-1" title="Lien à transmettre au fournisseur">
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Aucun fournisseur invité.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-5">
        @if($req->notes)<div class="rounded-lg border border-amber-200 bg-amber-50 text-amber-800 p-4 text-sm">{{ $req->notes }}</div>@endif

        @if($req->status === 'open')
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-3">
                <p class="text-sm text-gray-600">Réponses attendues jusqu'à <strong>{{ $req->deadline_at->format('H:i') }}</strong>. À l'échéance, la meilleure offre est retenue automatiquement.</p>
                <form method="POST" action="{{ route('admin.sourcing.award', $req->id) }}" onsubmit="return confirm('Clôturer maintenant et retenir la meilleure offre ?')">
                    @csrf<button class="w-full px-4 py-2.5 bg-green-600 text-white font-bold rounded-md text-sm hover:bg-green-700">Choisir la meilleure offre maintenant</button>
                </form>
            </div>
        @endif

        @if($req->status === 'needs_location')
            <div class="bg-white rounded-lg shadow-sm border border-red-100 p-5 space-y-2">
                <h3 class="text-xs font-bold text-red-500 uppercase tracking-wider">Renseigner la position</h3>
                <form method="POST" action="{{ route('admin.sourcing.location', $req->id) }}" class="space-y-2">
                    @csrf
                    <input name="latitude" type="number" step="any" required placeholder="Latitude (ex : 14.7167)" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <input name="longitude" type="number" step="any" required placeholder="Longitude (ex : -17.4677)" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <button class="w-full px-4 py-2.5 bg-navy-900 text-white font-bold rounded-md text-sm">Enregistrer et lancer</button>
                </form>
            </div>
        @endif

        @if(in_array($req->status, ['failed', 'cancelled'], true))
            <form method="POST" action="{{ route('admin.sourcing.relaunch', $req->id) }}">
                @csrf<button class="w-full px-4 py-2.5 bg-navy-900 text-white font-bold rounded-md text-sm">Relancer l'appel d'offres</button>
            </form>
        @endif

        @if(in_array($req->status, ['open', 'failed', 'needs_location'], true))
            <form method="POST" action="{{ route('admin.sourcing.cancel', $req->id) }}" onsubmit="return confirm('Annuler cet appel d\'offres ?')">
                @csrf<button class="w-full px-4 py-2 text-sm font-bold text-red-600 border border-red-200 rounded-md hover:bg-red-50">Annuler</button>
            </form>
        @endif

        @if($req->status === 'awarded' && $req->awardedOffer)
            <div class="rounded-lg border border-green-200 bg-green-50 text-green-800 p-4 text-sm">
                Retenue : <strong>{{ $req->awardedOffer->supplier->name }}</strong> ({{ $req->award_mode === 'manual' ? 'choix manuel' : 'choix automatique' }}).<br>
                Prochaine étape : attribution de la livraison à un livreur.
            </div>
        @endif
    </div>
</div>
@endsection
