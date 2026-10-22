@extends('layouts.app')

@section('title', 'Vérification Care+ - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-md mx-auto px-4">
        <div class="text-center mb-6">
            <p class="text-xs font-bold tracking-widest text-gold-600 uppercase">IT Holding Services</p>
            <h1 class="mt-1 text-xl font-semibold text-navy-900">Vérification de certificat</h1>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            @if (!$subscription)
                <p class="text-center text-red-700 font-semibold">Certificat introuvable.</p>
                <p class="mt-2 text-center text-sm text-gray-500">Ce code ne correspond à aucun certificat délivré par IT Holding Services.</p>
            @else
                @php
                    $valid = $subscription->payment_status === 'paid'
                        && $subscription->status === 'active'
                        && $subscription->device_status === 'approved'
                        && !$subscription->end_date->isPast();
                @endphp
                <p class="text-center font-semibold {{ $valid ? 'text-green-700' : 'text-red-700' }}">
                    {{ $valid ? 'Certificat valide' : 'Certificat non valide ou expiré' }}
                </p>
                <dl class="mt-5 divide-y divide-gray-100 text-sm">
                    <div class="py-2.5 flex justify-between"><dt class="text-gray-500">N°</dt><dd class="font-semibold text-navy-900">{{ $subscription->number }}</dd></div>
                    <div class="py-2.5 flex justify-between"><dt class="text-gray-500">Formule</dt><dd class="font-semibold text-navy-900">{{ ucfirst($subscription->plan) }}</dd></div>
                    <div class="py-2.5 flex justify-between"><dt class="text-gray-500">Appareil</dt><dd class="font-semibold text-navy-900">{{ $subscription->product_name }}</dd></div>
                    <div class="py-2.5 flex justify-between"><dt class="text-gray-500">N° de série</dt><dd class="font-semibold text-navy-900">…{{ substr((string) $subscription->serial_number, -4) }}</dd></div>
                    <div class="py-2.5 flex justify-between"><dt class="text-gray-500">Validité</dt><dd class="font-semibold text-navy-900">{{ $subscription->start_date->format('d/m/Y') }} → {{ $subscription->end_date->format('d/m/Y') }}</dd></div>
                </dl>
            @endif
        </div>
    </div>
</div>
@endsection
