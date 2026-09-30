@extends('layouts.app')

@section('title', 'Missions - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="bg-white border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center italic">
                <a href="{{ route('home') }}" class="hover:text-navy-900">Accueil</a>
                <span>›</span>
                <a href="{{ route('dashboard.partner') }}" class="hover:text-navy-900 uppercase tracking-wider">Espace Partenaire</a>
                <span>›</span>
                <span class="text-navy-900 font-bold uppercase tracking-wider">Missions</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('layouts.client_sidebar')

            <main class="flex-1 min-w-0 space-y-8">
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-xs font-bold italic">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-xs font-bold italic">{{ session('error') }}</div>
                @endif

                @include('pages.shop.partner._tabs', ['active' => 'missions'])

                @if($user->pendingContract())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-xs text-red-700">
                    Vous pouvez postuler, mais aucune mission ne pourra vous être payée tant que vous n'avez pas <a href="{{ route('dashboard.partner.contract') }}" class="font-bold underline">accepté votre contrat</a>.
                </div>
                @endif

                {{-- My applications --}}
                @if($myApplications->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Mes candidatures & projets</h3>
                    </div>
                    <ul class="divide-y divide-gray-50">
                        @foreach($myApplications as $app)
                        <li class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('dashboard.partner.missions.show', $app->mission_id) }}" class="text-sm font-black text-navy-900 hover:text-gold-600">{{ $app->mission->title }}</a>
                                <div class="text-[11px] text-gray-400 font-mono">{{ $app->mission->reference }}@if($app->mission->project_ref && $app->isTeamMember()) · Projet {{ $app->mission->project_ref }}@endif</div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $app->status_classes }}">{{ $app->status_label }}</span>
                                @if($app->isTeamMember() && $app->mission->hasWorkspace())
                                    <a href="{{ route('dashboard.partner.missions.workspace', $app->mission_id) }}" class="bg-navy-900 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg">Espace projet</a>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Open missions --}}
                <div>
                    <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic mb-4">Missions disponibles</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($openMissions as $mission)
                            <a href="{{ route('dashboard.partner.missions.show', $mission->id) }}" class="block bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-gold-300 transition">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="text-sm font-black text-navy-900">{{ $mission->title }}</h4>
                                    @if(in_array($mission->id, $appliedMissionIds))
                                        <span class="shrink-0 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Déjà postulé</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $mission->description }}</p>
                                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-3 text-[11px] text-gray-600">
                                    @if($mission->city)<span>📍 {{ $mission->city }}</span>@endif
                                    @if($mission->budget)<span>💰 {{ number_format($mission->budget, 0, ',', ' ') }} FCFA</span>@endif
                                    @if($mission->duration)<span>⏱ {{ $mission->duration }}</span>@endif
                                    <span>👥 {{ $mission->positions }} pers.</span>
                                </div>
                                @if($mission->target_types)
                                    <span class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold bg-gold-50 text-gold-700 border border-gold-200">{{ $mission->target_label }}</span>
                                @endif
                                @if($mission->apply_until)
                                    <div class="text-[10px] text-gray-400 mt-2">Candidatures jusqu'au {{ $mission->apply_until->format('d/m/Y') }}</div>
                                @endif
                            </a>
                        @empty
                            <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center text-xs text-gray-400 italic">
                                Aucune mission ouverte pour le moment. Complétez votre profil (compétences, ville) : IT Holding s'en sert pour vous proposer les missions adaptées.
                            </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
