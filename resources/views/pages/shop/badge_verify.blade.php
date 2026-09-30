@extends('layouts.app')

@section('title', 'Vérification de Badge - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen py-12 flex flex-col justify-center sm:px-6 lg:px-8">
    <div class="max-w-md w-full mx-auto">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-navy-900 tracking-tighter uppercase italic">
                IT-HOLDING <span class="text-gold-500">Badge Officiel</span>
            </h2>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Vérification d'authenticité</p>
        </div>

        <div class="bg-white py-8 px-6 shadow-xl rounded-2xl border border-gray-100 sm:px-10">
            @if($userBadge && $userBadge->user)
                @php
                    $state = $userBadge->display_status;
                    $holderActive = $userBadge->user->role === 'partner' && $userBadge->user->partner_status === 'approved';
                    $ok = $state === 'valid' && $holderActive;
                @endphp

                <div class="text-center mb-6">
                    <div class="text-5xl mb-3">{{ $userBadge->badge->icon }}</div>
                    <h3 class="text-lg font-black text-navy-900 uppercase tracking-tight italic">{{ $userBadge->badge->name }}</h3>
                    <p class="text-xs text-gray-400 font-bold mt-1 font-mono">{{ $userBadge->number }}</p>
                    <div class="mt-4">
                        @if($ok)
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase bg-green-50 text-green-700 border border-green-200">Badge valide</span>
                        @elseif($state === 'revoked')
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase bg-red-50 text-red-700 border border-red-200">Badge révoqué</span>
                        @elseif($state === 'expired')
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase bg-red-50 text-red-700 border border-red-200">Badge expiré</span>
                        @else
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase bg-red-50 text-red-700 border border-red-200">Titulaire non actif</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-4 border-t border-b border-gray-100 py-6 mb-6">
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Titulaire</span>
                        <span class="text-sm font-bold text-navy-900">{{ $userBadge->user->name }}</span>
                        @if($userBadge->user->professionalProfile?->pro_id)
                            <a href="{{ route('professional.verify', $userBadge->user->professionalProfile->pro_id) }}" class="block text-xs font-mono text-gold-600 hover:underline">{{ $userBadge->user->professionalProfile->pro_id }}</a>
                        @endif
                    </div>
                    @if($userBadge->badge->description)
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Signification</span>
                        <span class="text-sm text-gray-700">{{ $userBadge->badge->description }}</span>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Délivré le</span>
                            <span class="text-sm font-bold text-gray-800">{{ $userBadge->issued_at->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Expire le</span>
                            <span class="text-sm font-bold {{ $ok ? 'text-green-600' : 'text-red-600' }}">{{ $userBadge->expires_at?->format('d/m/Y') ?? 'Sans expiration' }}</span>
                        </div>
                    </div>
                </div>

                <div class="text-center text-[10px] text-gray-400 leading-normal">
                    Ce badge a été délivré par IT HOLDING SÉNÉGAL. Seule cette page fait foi de sa validité.
                </div>
            @else
                <div class="text-center py-6">
                    <h3 class="text-lg font-black text-navy-900 uppercase tracking-tight italic">Badge introuvable</h3>
                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                        Aucun badge ne correspond au numéro : <strong class="text-red-600 font-mono font-bold">{{ $number }}</strong>.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
