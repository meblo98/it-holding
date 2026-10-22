@extends('layouts.app')

@section('title', 'Souscription - ' . config('app.name'))

@section('content')
@php $paid = $subscription->payment_status === 'paid'; @endphp
<section class="bg-white">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        @if ($paid)
            <p class="text-sm font-semibold text-gold-600">Paiement confirmé</p>
            <h1 class="mt-2 text-3xl font-semibold text-navy-900 tracking-tight">Votre protection est activée.</h1>
            <dl class="mt-8 text-navy-600 divide-y divide-navy-100 border-y border-navy-100">
                <div class="py-3 flex justify-between gap-4"><dt>Numéro</dt><dd class="font-semibold text-navy-900">{{ $subscription->number }}</dd></div>
                <div class="py-3 flex justify-between gap-4"><dt>Formule</dt><dd class="font-semibold text-navy-900">{{ ucfirst($subscription->plan) }}</dd></div>
                <div class="py-3 flex justify-between gap-4"><dt>Valable</dt><dd class="font-semibold text-navy-900">du {{ $subscription->start_date->format('d/m/Y') }} au {{ $subscription->end_date->format('d/m/Y') }}</dd></div>
                <div class="py-3 flex justify-between gap-4"><dt>Montant payé</dt><dd class="font-semibold text-navy-900">{{ number_format($subscription->amount_paid, 0, ',', ' ') }} FCFA</dd></div>
            </dl>
            <p class="mt-6 text-navy-500">Prochaine étape : enregistrez votre appareil pour que notre équipe valide son admissibilité. Votre certificat sera ensuite disponible.</p>
            <a href="{{ route('dashboard.care') }}" class="mt-4 inline-flex btn-primary-gold">Enregistrer mon appareil</a>
        @else
            <p class="text-sm font-semibold text-gold-600">Paiement en cours de confirmation</p>
            <h1 class="mt-2 text-3xl font-semibold text-navy-900 tracking-tight">Nous attendons la confirmation de votre paiement.</h1>
            <p class="mt-4 text-navy-500">Cela prend généralement quelques instants. Votre protection sera activée dès que le paiement sera confirmé. Rafraîchissez cette page dans une minute. Si rien ne change, contactez-nous en indiquant le numéro {{ $subscription->number }}.</p>
            <a href="{{ url()->current() }}?{{ http_build_query(request()->query()) }}" class="mt-6 inline-flex px-5 py-2.5 rounded-md border border-navy-200 font-semibold text-navy-900">Rafraîchir</a>
        @endif
        <div class="mt-10"><a href="{{ route('home') }}" class="btn-primary-gold">Retour à l’accueil</a></div>
    </div>
</section>
@endsection
