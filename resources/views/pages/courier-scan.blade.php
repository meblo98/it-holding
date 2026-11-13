@extends('layouts.app')

@section('title', $title . ' - ' . config('app.name'))

@section('content')
<section class="bg-gray-50 min-h-screen">
    <div class="max-w-lg mx-auto px-4 py-10">
        <p class="text-xs font-bold tracking-widest text-gold-600 uppercase">IT Holding Services</p>
        <h1 class="mt-1 text-2xl font-semibold text-navy-900">{{ $title }}</h1>
        <p class="text-sm text-gray-500">{{ $detail }}</p>

        <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
            @if ($valid)
                <p class="text-sm text-gray-700">
                    @if ($mode === 'pickup') Vous avez scanné le bon de commande. Confirmez uniquement si le fournisseur vous a bien remis le colis.
                    @else Vous avez scanné le code du client. Confirmez uniquement si vous lui avez bien remis le colis. @endif
                </p>
                <form method="POST" action="{{ $action }}">
                    @csrf
                    <button class="w-full btn-primary-gold justify-center">{{ $button }}</button>
                </form>
            @else
                <p class="text-sm text-red-700">Cette course n'est pas disponible pour vous : elle est déjà validée, ou elle est affectée à un autre livreur.</p>
            @endif
            <a href="{{ route('courier.home') }}" class="block text-center text-sm text-gray-500 underline">Retour à mes courses</a>
        </div>
    </div>
</section>
@endsection
