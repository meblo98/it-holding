@extends('layouts.app')

@section('title', 'Vérification de Garantie - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen py-12 flex flex-col justify-center sm:px-6 lg:px-8">
    <div class="max-w-md w-full mx-auto">
        <!-- Logo / Header -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-navy-900 tracking-tighter uppercase italic">
                IT-HOLDING <span class="text-gold-500">Service Garantie</span>
            </h2>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Authentification Officielle</p>
        </div>

        <div class="bg-white py-8 px-6 shadow-xl rounded-2xl border border-gray-100 sm:px-10">
            @if(isset($warranty))
                @php
                    $config = \App\Models\Warranty::statusConfig($warranty->status);
                    $isActive = $warranty->status === 'active' && !$warranty->is_expired;
                @endphp

                <!-- Status Banner -->
                <div class="text-center mb-6">
                    @if($isActive)
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-50 text-green-500 border-2 border-green-200 mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04c0 4.835 1.355 9.347 3.718 13.191A11.96 11.96 0 0012 21.481c2.901 0 5.537-.94 7.653-2.545a11.959 11.959 0 013.718-13.191z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-green-700 uppercase tracking-tight italic">Garantie Certifiée Active</h3>
                    @else
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 text-red-500 border-2 border-red-200 mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-red-700 uppercase tracking-tight italic">Garantie non-active / expiré</h3>
                    @endif
                    <p class="text-xs text-gray-400 font-bold mt-1">Garantie N° {{ $warranty->number }}</p>
                </div>

                <!-- Details List -->
                <div class="space-y-4 border-t border-b border-gray-100 py-6 mb-6">
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Produit couvert</span>
                        <span class="text-sm font-bold text-navy-900">{{ $warranty->product_name }}</span>
                    </div>

                    @if($warranty->serial_number)
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Numéro de Série (S/N)</span>
                        <span class="text-sm font-mono font-bold text-gray-800">{{ $warranty->serial_number }}</span>
                    </div>
                    @endif

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Bénéficiaire</span>
                        <span class="text-sm font-bold text-navy-900">
                            {{ $warranty->client_name }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Date d'achat</span>
                            <span class="text-sm font-bold text-gray-800">{{ $warranty->purchase_date->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Date d'expiration</span>
                            <span class="text-sm font-bold text-gray-800 {{ $isActive ? 'text-green-600' : 'text-red-600' }}">
                                {{ $warranty->expiry_date->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Type de couverture</span>
                        <span class="text-sm font-bold text-navy-900">
                            {{ \App\Models\Warranty::typeLabel($warranty->type) }}
                        </span>
                    </div>

                    @if($isActive)
                    <div class="bg-green-50 p-3 rounded-lg text-center border border-green-100">
                        <span class="text-xs font-black text-green-700 uppercase tracking-wider block">
                            {{ $warranty->days_remaining }} jours de couverture restants
                        </span>
                    </div>
                    @endif
                </div>

                <div class="text-center text-[10px] text-gray-400 leading-normal">
                    Ce QR Code certifie que le produit ci-dessus est enregistré dans le système officiel de suivi de garantie de IT HOLDING SÉNÉGAL.
                </div>
            @else
                <div class="text-center py-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 text-red-500 mb-4">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-navy-900 uppercase tracking-tight italic">Garantie Introuvable</h3>
                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                        Aucun enregistrement ne correspond au code ou numéro de série : <strong class="text-red-600 font-mono font-bold">{{ $number }}</strong>.
                    </p>
                    <a href="{{ route('shop.index') }}" class="mt-6 inline-block w-full py-2 bg-navy-600 hover:bg-navy-700 text-white text-xs font-bold uppercase tracking-widest rounded-lg shadow transition">
                        Retour à la boutique
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
