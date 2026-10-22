@extends('layouts.app')

@section('title', 'Certificat ' . $subscription->number . ' - ' . config('app.name'))

@section('content')
@php $fmt = fn ($n) => number_format($n, 0, ',', ' ') . ' FCFA'; @endphp
<style>@media print { header, footer, nav, .no-print { display: none !important; } body { background: #fff; } }</style>
<div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-2xl mx-auto px-4">
        <div class="no-print mb-4 flex items-center justify-between">
            <a href="{{ route('dashboard.care') }}" class="text-sm text-gray-500 hover:text-navy-900">← Mon Care+</a>
            <button onclick="window.print()" class="px-4 py-2 rounded-md bg-navy-900 text-white text-sm font-semibold">Imprimer / enregistrer en PDF</button>
        </div>

        <div class="bg-white border-2 border-navy-900 rounded-lg p-8">
            <div class="text-center">
                <p class="text-xs font-bold tracking-widest text-gold-600 uppercase">IT Holding Services</p>
                <h1 class="mt-1 text-2xl font-semibold text-navy-900">Certificat de Garantie &amp; Protection</h1>
                <p class="mt-1 text-sm text-gray-500">N° {{ $subscription->number }}</p>
            </div>

            <dl class="mt-8 divide-y divide-gray-100 text-sm">
                <div class="py-3 flex justify-between gap-4"><dt class="text-gray-500">Titulaire</dt><dd class="font-semibold text-navy-900 text-right">{{ $subscription->client_name }}</dd></div>
                <div class="py-3 flex justify-between gap-4"><dt class="text-gray-500">Formule</dt><dd class="font-semibold text-navy-900">{{ ucfirst($subscription->plan) }}</dd></div>
                <div class="py-3 flex justify-between gap-4"><dt class="text-gray-500">Appareil</dt><dd class="font-semibold text-navy-900 text-right">{{ $subscription->product_name }}</dd></div>
                <div class="py-3 flex justify-between gap-4"><dt class="text-gray-500">N° de série</dt><dd class="font-semibold text-navy-900">{{ $subscription->serial_number }}</dd></div>
                <div class="py-3 flex justify-between gap-4"><dt class="text-gray-500">Période de validité</dt><dd class="font-semibold text-navy-900">du {{ $subscription->start_date->format('d/m/Y') }} au {{ $subscription->end_date->format('d/m/Y') }}</dd></div>
                @if ($plan)
                    <div class="py-3 flex justify-between gap-4"><dt class="text-gray-500">Prise en charge</dt><dd class="font-semibold text-navy-900">{{ $plan['coverage_rate'] }} % des frais admissibles</dd></div>
                    <div class="py-3 flex justify-between gap-4"><dt class="text-gray-500">Plafond par réparation</dt><dd class="font-semibold text-navy-900">{{ $fmt($plan['per_repair_cap']) }}</dd></div>
                    <div class="py-3 flex justify-between gap-4"><dt class="text-gray-500">Plafond sur 12 mois</dt><dd class="font-semibold text-navy-900">{{ $fmt($plan['per_year_cap']) }}</dd></div>
                @endif
            </dl>

            <div class="mt-8 flex items-center gap-6">
                <img src="{{ $qrUrl }}" alt="QR code de vérification" width="120" height="120" class="border border-gray-200 rounded">
                <p class="text-xs text-gray-500 leading-relaxed">
                    Scannez ce code pour vérifier l'authenticité du certificat. La page de vérification n'affiche aucune information personnelle.<br>
                    <span class="break-all">{{ $verifyUrl }}</span>
                </p>
            </div>

            <p class="mt-8 text-[11px] text-gray-400 leading-relaxed">
                Garantie commerciale de réparation complémentaire, distincte de la garantie du fabricant et des garanties légales. Elle ne constitue pas une assurance. La prise en charge s'applique aux frais admissibles approuvés après diagnostic, dans la limite des plafonds, et sous réserve des conditions et exclusions acceptées à la souscription.
            </p>
        </div>
    </div>
</div>
@endsection
