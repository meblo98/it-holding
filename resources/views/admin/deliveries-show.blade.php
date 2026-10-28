@extends('layouts.admin')
@section('title', 'Livraison commande #' . $delivery->order_id)
@section('content')
@php $order = $delivery->order; $open = in_array($delivery->status, ['pending', 'offered', 'accepted'], true); @endphp

<div class="mb-6">
    <a href="{{ route('admin.deliveries.index') }}" class="text-sm text-gray-400 hover:text-gray-700">← Livraisons</a>
    <div class="mt-1 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Livraison · commande #{{ $order->id }}</h1>
        <span class="px-3 py-1 rounded-full text-sm font-bold bg-blue-50 text-blue-700 border border-blue-200">{{ $delivery->status_label }}</span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-3 text-sm">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Trajet</h2>
            <p><span class="text-gray-400">Retrait :</span> <strong>{{ $delivery->pickup_label ?: '—' }}</strong> @if($delivery->pickup_maps_url)<a href="{{ $delivery->pickup_maps_url }}" target="_blank" class="underline text-navy-700">carte</a>@endif</p>
            <p><span class="text-gray-400">Livraison :</span> <strong>{{ $order->customer_name }}</strong> · {{ $order->customer_phone }}<br>{{ $order->customer_address }} @if($delivery->maps_url)<a href="{{ $delivery->maps_url }}" target="_blank" class="underline text-navy-700">carte</a>@endif</p>
            <p><span class="text-gray-400">Livreur :</span> <strong>{{ $delivery->courier?->name ?? '—' }}</strong>@if($delivery->distance_km !== null) · à {{ number_format($delivery->distance_km, 1) }} km du retrait @endif</p>
            @if($delivery->offer)<p><span class="text-gray-400">Fournisseur retenu :</span> {{ $delivery->offer->supplier->name }} ({{ number_format($delivery->offer->price, 0, ',', ' ') }} FCFA)</p>@endif
            @if($order->payment_method === 'cod')<p class="font-bold text-red-700">Paiement à la livraison : {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA à encaisser</p>@endif
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Étapes</h2>
            <ul class="text-sm space-y-1">
                <li>Proposée : {{ $delivery->offered_at?->format('d/m H:i') ?? '—' }}</li>
                <li>Acceptée : {{ $delivery->accepted_at?->format('d/m H:i') ?? '—' }}</li>
                <li>Colis récupéré : {{ $delivery->picked_up_at?->format('d/m H:i') ?? '—' }}</li>
                <li>Livrée : {{ $delivery->delivered_at?->format('d/m H:i') ?? '—' }}</li>
            </ul>
            @if($delivery->status === 'delivered')
                <p class="mt-4 text-sm text-green-700">Livrée. N'oubliez pas de passer la commande à « terminée » : cela déclenche les commissions partenaires.</p>
                <a href="{{ route('admin.orders.show', $order->id) }}" class="mt-2 inline-block text-sm font-bold text-navy-700 underline">Ouvrir la commande</a>
            @endif
        </div>
    </div>

    <div class="space-y-5">
        @if($open)
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-3">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Affecter un livreur</h3>
                <form method="POST" action="{{ route('admin.deliveries.assign', $delivery->id) }}" class="space-y-2">
                    @csrf
                    <select name="courier_id" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        <option value="">Choisir…</option>
                        @foreach($couriers as $c)
                            @php $p = $profiles[$c->id] ?? null; @endphp
                            <option value="{{ $c->id }}">{{ $c->name }}{{ $p && $p->is_available ? ' (disponible)' : '' }}</option>
                        @endforeach
                    </select>
                    <button class="w-full px-4 py-2.5 bg-navy-900 text-white font-bold rounded-md text-sm">Affecter directement</button>
                </form>
                <form method="POST" action="{{ route('admin.deliveries.reassign', $delivery->id) }}">
                    @csrf<button class="w-full px-4 py-2.5 border border-navy-900 text-navy-900 font-bold rounded-md text-sm">Chercher un autre livreur</button>
                </form>
                <form method="POST" action="{{ route('admin.deliveries.cancel', $delivery->id) }}" onsubmit="return confirm('Annuler cette livraison ?')">
                    @csrf<button class="w-full px-4 py-2 text-sm font-bold text-red-600 border border-red-200 rounded-md hover:bg-red-50">Annuler la livraison</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
