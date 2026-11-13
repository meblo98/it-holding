@extends('layouts.app')

@section('title', 'Mes revenus - ' . config('app.name'))

@section('content')
@php $fmt = fn ($n) => number_format($n, 0, ',', ' ') . ' FCFA'; @endphp
<section class="bg-gray-50 min-h-screen">
    <div class="max-w-lg mx-auto px-4 py-10">
        <a href="{{ route('courier.home') }}" class="text-sm text-gray-500">← Mes courses</a>
        <h1 class="mt-2 text-2xl font-semibold text-navy-900">Mes revenus</h1>

        <div class="mt-6 grid grid-cols-2 gap-3">
            <div class="bg-white rounded-xl border border-gray-100 p-4"><p class="text-xs text-gray-400 uppercase font-bold">Aujourd'hui</p><p class="mt-1 text-xl font-black text-navy-900">{{ $fmt($today) }}</p></div>
            <div class="bg-white rounded-xl border border-gray-100 p-4"><p class="text-xs text-gray-400 uppercase font-bold">Cette semaine</p><p class="mt-1 text-xl font-black text-navy-900">{{ $fmt($week) }}</p></div>
            <div class="bg-white rounded-xl border border-gray-100 p-4"><p class="text-xs text-gray-400 uppercase font-bold">Ce mois</p><p class="mt-1 text-xl font-black text-navy-900">{{ $fmt($month) }}</p></div>
            <div class="bg-white rounded-xl border border-gray-100 p-4"><p class="text-xs text-gray-400 uppercase font-bold">Total ({{ $count }} courses)</p><p class="mt-1 text-xl font-black text-navy-900">{{ $fmt($total) }}</p></div>
        </div>

        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            <strong>À recevoir : {{ $fmt($unpaid) }}</strong><br>
            <span class="text-xs">Le reste vous a déjà été payé par IT Holding.</span>
        </div>

        <div class="mt-6 bg-white rounded-xl border border-gray-100 p-5">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Mes dernières courses</p>
            <ul class="divide-y divide-gray-100 text-sm">
                @forelse ($rows as $d)
                    <li class="py-2.5 flex items-center justify-between gap-3">
                        <span>Commande #{{ $d->order_id }}<span class="block text-xs text-gray-400">{{ $d->delivered_at?->format('d/m/Y H:i') }}</span></span>
                        <span class="text-right"><span class="font-bold text-navy-900">{{ $fmt($d->courier_fee) }}</span>
                            <span class="block text-xs {{ $d->fee_paid_at ? 'text-green-600' : 'text-amber-600' }}">{{ $d->fee_paid_at ? 'payé le ' . $d->fee_paid_at->format('d/m') : 'à recevoir' }}</span></span>
                    </li>
                @empty
                    <li class="py-4 text-center text-gray-400">Aucune course terminée pour le moment.</li>
                @endforelse
            </ul>
        </div>
    </div>
</section>
@endsection
