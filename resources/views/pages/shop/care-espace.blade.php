@extends('layouts.app')

@section('title', 'Mon Care+ - ' . config('app.name'))

@section('content')
@php $fmt = fn ($n) => number_format($n, 0, ',', ' ') . ' FCFA'; @endphp
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('layouts.client_sidebar')

            <main class="flex-1 space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-semibold text-navy-900">Mon Care+</h1>
                        <p class="text-sm text-gray-500">Vos abonnements Garantie &amp; Protection, vos appareils et vos certificats.</p>
                    </div>
                    <a href="{{ route('care.landing') }}#formules" class="text-sm font-semibold text-navy-900 underline">Souscrire une formule</a>
                </div>

                @foreach (['success', 'error'] as $flash)
                    @if (session($flash))
                        <div class="rounded-lg border px-4 py-3 text-sm {{ $flash === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }}">{{ session($flash) }}</div>
                    @endif
                @endforeach

                @forelse ($subscriptions as $sub)
                    @php
                        $status = \App\Models\CareSubscription::statusConfig($sub->status);
                        $plan = $plans[$sub->plan] ?? null;
                        $deviceLabels = [
                            'none'     => ['Appareil à enregistrer', 'bg-amber-50 text-amber-700 border-amber-200'],
                            'pending'  => ['Vérification en cours', 'bg-blue-50 text-blue-700 border-blue-200'],
                            'approved' => ['Appareil validé', 'bg-green-50 text-green-700 border-green-200'],
                            'rejected' => ['Appareil refusé', 'bg-red-50 text-red-700 border-red-200'],
                        ];
                        [$dLabel, $dClass] = $deviceLabels[$sub->device_status ?: 'none'];
                    @endphp
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $sub->number }}</p>
                                <h2 class="text-lg font-semibold text-navy-900">Formule {{ ucfirst($sub->plan) }}</h2>
                            </div>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $status['classes'] }}">{{ $status['label'] }}</span>
                        </div>

                        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div><dt class="text-gray-400 text-xs">Prix</dt><dd class="font-semibold text-navy-900">{{ $fmt($sub->price) }}</dd></div>
                            <div><dt class="text-gray-400 text-xs">Paiement</dt><dd class="font-semibold {{ $sub->payment_status === 'paid' ? 'text-green-700' : 'text-amber-700' }}">{{ $sub->payment_status === 'paid' ? 'Payé' : 'En attente' }}</dd></div>
                            <div><dt class="text-gray-400 text-xs">Valable jusqu'au</dt><dd class="font-semibold text-navy-900">{{ $sub->payment_status === 'paid' ? $sub->end_date->format('d/m/Y') : '—' }}</dd></div>
                            <div><dt class="text-gray-400 text-xs">Prise en charge</dt><dd class="font-semibold text-navy-900">{{ $plan ? $plan['coverage_rate'] . ' %' : '—' }}</dd></div>
                        </dl>

                        @if ($sub->payment_status === 'paid')
                            <div class="border-t border-gray-100 pt-4 space-y-3">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-navy-900">{{ $sub->device_status === 'none' || !$sub->device_status ? 'Aucun appareil enregistré' : $sub->product_name }}</p>
                                        @if ($sub->serial_number && $sub->device_status !== 'none')
                                            <p class="text-xs text-gray-500">N° de série : {{ $sub->serial_number }}</p>
                                        @endif
                                    </div>
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $dClass }}">{{ $dLabel }}</span>
                                </div>

                                @if ($sub->device_status === 'rejected' && $sub->device_rejection_reason)
                                    <p class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2">Motif : {{ $sub->device_rejection_reason }}</p>
                                @endif
                                @if ($sub->device_status === 'pending')
                                    <p class="text-sm text-gray-500">Notre équipe vérifie l'admissibilité de votre appareil. Votre certificat sera disponible dès la validation.</p>
                                @endif

                                <div class="flex flex-wrap gap-2">
                                    @if ($sub->can_register_device)
                                        <a href="{{ route('dashboard.care.device', $sub->id) }}" class="btn-primary-gold">{{ $sub->device_status === 'rejected' ? 'Corriger et renvoyer' : 'Enregistrer mon appareil' }}</a>
                                    @endif
                                    @if ($sub->has_certificate)
                                        <a href="{{ route('dashboard.care.certificate', $sub->id) }}" class="inline-flex px-5 py-2.5 rounded-md bg-navy-900 text-white font-semibold text-sm">Voir mon certificat</a>
                                    @endif
                                    @if ($sub->can_declare_claim)
                                        <a href="{{ route('dashboard.care.claim.form', $sub->id) }}" class="inline-flex px-5 py-2.5 rounded-md border border-navy-900 text-navy-900 font-semibold text-sm">Déclarer une panne</a>
                                    @endif
                                </div>

                                @if ($sub->claims->isNotEmpty())
                                    <div class="pt-2">
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Mes demandes de réparation</p>
                                        <ul class="divide-y divide-gray-100 text-sm">
                                            @foreach ($sub->claims as $claim)
                                                <li class="py-2 flex items-center justify-between gap-3">
                                                    <a href="{{ route('dashboard.care.claim.show', $claim->id) }}" class="font-semibold text-navy-900 underline">{{ $claim->number }}</a>
                                                    <span class="text-gray-500">{{ $claim->symptoms }}</span>
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 whitespace-nowrap">{{ $claim->status_label }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-xl border border-gray-100 p-10 text-center">
                        <p class="text-navy-900 font-semibold">Vous n'avez pas encore d'abonnement.</p>
                        <p class="mt-1 text-sm text-gray-500">Choisissez une formule pour protéger vos ordinateurs.</p>
                        <a href="{{ route('care.landing') }}#formules" class="mt-5 inline-flex btn-primary-gold">Voir les formules</a>
                    </div>
                @endforelse
            </main>
        </div>
    </div>
</div>
@endsection
