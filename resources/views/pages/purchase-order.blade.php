@extends('layouts.app')

@section('title', 'Bon de commande ' . $bon->number . ' - ' . config('app.name'))

@section('content')
@php $handed = $bon->status === 'handed_over'; @endphp
<style>@media print { header, footer, nav, .no-print { display: none !important; } body { background: #fff; } }</style>
<section class="bg-gray-50 min-h-screen">
    <div class="max-w-lg mx-auto px-4 py-10">
        <div class="no-print mb-4 text-right">
            <button onclick="window.print()" class="px-4 py-2 rounded-md bg-navy-900 text-white text-sm font-semibold">Imprimer</button>
        </div>

        <div class="bg-white border-2 border-navy-900 rounded-lg p-6 space-y-5">
            <div class="text-center">
                <p class="text-xs font-bold tracking-widest text-gold-600 uppercase">IT Holding Services</p>
                <h1 class="mt-1 text-2xl font-semibold text-navy-900">Bon de commande</h1>
                <p class="text-sm text-gray-500">N° {{ $bon->number }} · {{ $bon->supplier->name }}</p>
            </div>

            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Articles à préparer</p>
                <ul class="divide-y divide-gray-100 text-sm">
                    @foreach ($items as $item)
                        <li class="py-2 flex justify-between gap-3"><span class="text-navy-900">{{ $item->product?->name ?? 'Produit' }}</span><span class="font-bold">× {{ $item->quantity }}</span></li>
                    @endforeach
                </ul>
            </div>

            <dl class="text-sm divide-y divide-gray-100 border-y border-gray-100">
                <div class="py-2 flex justify-between"><dt class="text-gray-500">Prix convenu</dt><dd class="font-bold text-navy-900">{{ number_format($bon->agreed_price, 0, ',', ' ') }} FCFA</dd></div>
                @if ($bon->delay_hours)<div class="py-2 flex justify-between"><dt class="text-gray-500">Délai de préparation</dt><dd class="font-bold text-navy-900">{{ $bon->delay_hours }} h</dd></div>@endif
                @if ($delivery?->courier)<div class="py-2 flex justify-between"><dt class="text-gray-500">Livreur attendu</dt><dd class="font-bold text-navy-900">{{ $delivery->courier->name }}</dd></div>@endif
            </dl>

            @if ($handed)
                <div class="rounded-lg border border-green-200 bg-green-50 text-green-800 p-4 text-sm text-center">
                    Remise validée le {{ $bon->handed_over_at?->format('d/m/Y à H:i') }}. Merci !
                </div>
            @elseif ($bon->status === 'cancelled')
                <div class="rounded-lg border border-red-200 bg-red-50 text-red-700 p-4 text-sm text-center">Ce bon est annulé.</div>
            @else
                <div class="text-center space-y-3">
                    <p class="text-sm text-gray-600">À la remise de la commande, le livreur <strong>scanne ce QR code</strong> ou vous <strong>donnez-lui ce code</strong> :</p>
                    <img src="{{ $bon->qr_image_url }}" alt="QR code du bon de commande" width="200" height="200" class="mx-auto border border-gray-200 rounded">
                    <p class="text-xs text-gray-400">Code de remise</p>
                    <p class="text-4xl font-black tracking-[0.3em] text-navy-900">{{ $bon->pickup_code }}</p>
                    <p class="text-xs text-gray-500">Ne remettez la commande qu'à un livreur qui présente ce code ou scanne ce QR.</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
