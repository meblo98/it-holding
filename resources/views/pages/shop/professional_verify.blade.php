@extends('layouts.app')

@section('title', 'Vérification Profil Professionnel - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen py-12 flex flex-col justify-center sm:px-6 lg:px-8">
    <div class="max-w-md w-full mx-auto">
        <!-- Logo / Header -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-navy-900 tracking-tighter uppercase italic">
                IT-HOLDING <span class="text-gold-500">Réseau Professionnel</span>
            </h2>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Vérification Officielle du Profil</p>
        </div>

        <div class="bg-white py-8 px-6 shadow-xl rounded-2xl border border-gray-100 sm:px-10">
            @if($profile && $profile->user)
                @php
                    $user = $profile->user;
                    $isActive = $user->role === 'partner' && $user->partner_status === 'approved';
                @endphp

                <!-- Status Banner -->
                <div class="text-center mb-6">
                    @if($isActive)
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-50 text-green-500 border-2 border-green-200 mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04c0 4.835 1.355 9.347 3.718 13.191A11.96 11.96 0 0012 21.481c2.901 0 5.537-.94 7.653-2.545a11.959 11.959 0 013.718-13.191z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-green-700 uppercase tracking-tight italic">Professionnel enregistré IT Holding</h3>
                    @else
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 text-red-500 border-2 border-red-200 mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-red-700 uppercase tracking-tight italic">Profil non actif</h3>
                    @endif
                    <p class="text-xs text-gray-400 font-bold mt-1 font-mono">{{ $profile->pro_id }}</p>
                </div>

                <!-- Details List -->
                <div class="space-y-4 border-t border-b border-gray-100 py-6 mb-6">
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Nom</span>
                        <span class="text-sm font-bold text-navy-900">{{ $user->name }}</span>
                    </div>

                    @if($profile->isStructure())
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Structure</span>
                        <span class="text-sm font-bold text-navy-900">{{ $profile->company_name }}</span>
                        @if($profile->ninea)<span class="block text-xs text-gray-500">NINEA {{ $profile->ninea }}</span>@endif
                    </div>
                    @endif

                    @if($user->partner_type)
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Catégorie</span>
                        <span class="text-sm font-bold text-navy-900">{{ $user->partner_type_label }}</span>
                    </div>
                    @endif

                    @if($profile->city)
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Ville</span>
                        <span class="text-sm font-bold text-gray-800">{{ $profile->city }}</span>
                    </div>
                    @endif

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Statut</span>
                        <span class="text-sm font-bold {{ $user->partner_status === 'approved' ? 'text-green-600' : 'text-gray-500' }}">
                            {{ $user->partner_status === 'approved' ? 'Actif' : ucfirst($user->partner_status ?? 'Inconnu') }}
                        </span>
                    </div>

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Niveau de vérification</span>
                        <span class="text-sm font-bold text-navy-900">Niveau {{ $profile->verification_level }}/5 — {{ $profile->verification_label }}</span>
                    </div>

                    @if($isActive && $badges->isNotEmpty())
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Badges en cours de validité</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($badges as $ub)
                                <a href="{{ route('badge.verify', $ub->number) }}" class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $ub->badge->color_classes }}">
                                    {{ $ub->badge->icon }} {{ $ub->badge->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="text-center text-[10px] text-gray-400 leading-normal">
                    @if($isActive)
                        Ce QR Code certifie que ce professionnel est réellement enregistré dans le réseau officiel IT HOLDING SÉNÉGAL.
                    @else
                        Ce profil existe mais n'est pas (ou plus) actif dans le réseau IT HOLDING SÉNÉGAL.
                    @endif
                </div>
            @else
                <div class="text-center py-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 text-red-500 mb-4">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-navy-900 uppercase tracking-tight italic">Profil Introuvable</h3>
                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                        Aucun professionnel enregistré ne correspond à l'identifiant : <strong class="text-red-600 font-mono font-bold">{{ $proId }}</strong>.
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
