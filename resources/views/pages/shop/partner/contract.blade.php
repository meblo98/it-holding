@extends('layouts.app')

@section('title', 'Contrat Partenaire - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center italic">
                <a href="{{ route('home') }}" class="hover:text-navy-900 flex items-center gap-1">
                    <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Accueil
                </a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('dashboard.partner') }}" class="hover:text-navy-900 transition-colors uppercase tracking-wider">Espace Partenaire</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-navy-900 font-bold uppercase tracking-wider">Contrat</span>
            </nav>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-xs font-bold italic mb-6">{{ session('success') }}</div>
        @endif

        @if(!$contract)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center">
                <h2 class="text-lg font-black text-navy-900 uppercase italic mb-2">Aucun contrat en vigueur</h2>
                <p class="text-sm text-gray-500">Il n'y a pas encore de contrat publié pour votre catégorie. Vous pouvez utiliser votre espace normalement.</p>
                <a href="{{ route('dashboard.partner') }}" class="mt-6 inline-block btn-primary-gold px-8 py-3 uppercase tracking-widest text-[10px]">Retour au tableau de bord</a>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-navy-900 text-white p-6">
                    <span class="text-gold-500 text-[10px] font-black uppercase tracking-widest">Contrat {{ \App\Models\User::PARTNER_TYPES[$contract->type] ?? $contract->type }} — v{{ $contract->version }}</span>
                    <h2 class="text-xl font-black uppercase italic mt-1">{{ $contract->title }}</h2>
                </div>

                <div class="p-6 lg:p-8 max-h-[60vh] overflow-y-auto border-b border-gray-100">
                    <div class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $contract->content }}</div>
                </div>

                <div class="p-6 lg:p-8">
                    @if($accepted)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm font-bold text-green-800">Contrat accepté le {{ $accepted->accepted_at->format('d/m/Y à H:i') }}</p>
                                <p class="text-xs text-green-600">Votre acceptation a été enregistrée (IP {{ $accepted->ip_address }}).</p>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('dashboard.partner.contract.accept', $contract->id) }}" method="POST">
                            @csrf
                            <label class="flex items-start gap-3 mb-4 cursor-pointer">
                                <input type="checkbox" required class="mt-1 rounded text-gold-500 focus:ring-gold-500 h-4 w-4 border-gray-300">
                                <span class="text-xs text-gray-600">J'ai lu l'intégralité de ce contrat et j'en accepte les termes et conditions.</span>
                            </label>
                            <button type="submit" class="btn-primary-gold px-8 py-3.5 text-xs font-black uppercase tracking-widest shadow-md">
                                J'ai lu et j'accepte le contrat
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
