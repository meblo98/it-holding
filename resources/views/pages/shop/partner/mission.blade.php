@extends('layouts.app')

@section('title', $mission->title . ' - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="bg-white border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center italic">
                <a href="{{ route('dashboard.partner') }}" class="hover:text-navy-900 uppercase tracking-wider">Espace Partenaire</a>
                <span>›</span>
                <a href="{{ route('dashboard.partner.missions') }}" class="hover:text-navy-900 uppercase tracking-wider">Missions</a>
                <span>›</span>
                <span class="text-navy-900 font-bold font-mono">{{ $mission->reference }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14 space-y-6">
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-xs font-bold italic">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-xs font-bold italic">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-navy-900 text-white p-6">
                <span class="text-gold-500 text-[10px] font-black uppercase tracking-widest">Mission {{ $mission->reference }}</span>
                <h1 class="text-xl lg:text-2xl font-black uppercase italic mt-1">{{ $mission->title }}</h1>
                <div class="flex flex-wrap gap-x-5 gap-y-1 mt-3 text-xs text-gray-300">
                    @if($mission->city)<span>📍 {{ $mission->city }}{{ $mission->location ? ' — ' . $mission->location : '' }}</span>@endif
                    @if($mission->budget)<span>💰 {{ number_format($mission->budget, 0, ',', ' ') }} FCFA</span>@endif
                    @if($mission->duration)<span>⏱ {{ $mission->duration }}</span>@endif
                    @if($mission->start_date)<span>📅 Début {{ $mission->start_date->format('d/m/Y') }}</span>@endif
                    <span>👥 {{ $mission->positions }} personne(s)</span>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $mission->description }}</div>
                @if($mission->required_skills)
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($mission->required_skills as $skill)
                            <span class="px-2.5 py-1 rounded-full bg-gray-100 text-[11px] font-bold text-gray-700">{{ $skill }}</span>
                        @endforeach
                    </div>
                @endif
                @if($mission->equipment)
                    <div class="text-xs text-gray-600"><span class="font-bold uppercase text-gray-400">Matériel :</span> {{ $mission->equipment }}</div>
                @endif
            </div>
        </div>

        @if($application)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <h2 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Ma candidature</h2>
                    <span class="px-2.5 py-1 rounded text-[11px] font-bold border {{ $application->status_classes }}">{{ $application->status_label }}</span>
                </div>

                {{-- Parcours (doc §16) --}}
                @if($application->status !== 'rejected')
                    @php $steps = array_keys(array_diff_key(\App\Models\MissionApplication::STATUSES, ['rejected' => true])); $currentIndex = array_search($application->status, $steps); @endphp
                    <ol class="flex flex-wrap gap-1 mt-4">
                        @foreach($steps as $i => $step)
                            <li class="px-2 py-1 rounded text-[10px] font-bold {{ $i <= $currentIndex ? 'bg-navy-900 text-white' : 'bg-gray-100 text-gray-400' }}">{{ \App\Models\MissionApplication::STATUSES[$step] }}</li>
                        @endforeach
                    </ol>
                @else
                    <p class="text-xs text-gray-500 mt-3">Votre candidature n'a pas été retenue pour cette mission. D'autres missions vous attendent.</p>
                @endif

                @if($application->status === 'paid')
                    <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3 text-xs text-green-800">
                        Mission payée le {{ $application->paid_at->format('d/m/Y') }} : {{ number_format($application->agreed_amount, 0, ',', ' ') }} FCFA
                        @if($application->withholding_amount > 0) − {{ number_format($application->withholding_amount, 0, ',', ' ') }} FCFA de retenue à la source @endif
                        = <strong>{{ number_format($application->net_amount, 0, ',', ' ') }} FCFA crédités sur votre portefeuille</strong>.
                    </div>
                @endif

                @if($application->isTeamMember() && $mission->hasWorkspace())
                    <a href="{{ route('dashboard.partner.missions.workspace', $mission->id) }}" class="mt-5 inline-block btn-primary-gold px-6 py-3 text-[10px] uppercase tracking-widest">Ouvrir l'espace projet {{ $mission->project_ref }}</a>
                @endif
            </div>
        @elseif($mission->isOpenForApplications())
            <form action="{{ route('dashboard.partner.missions.apply', $mission->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                @csrf
                <h2 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Postuler</h2>
                @if($errors->any())
                    <ul class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700 list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                @endif
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Votre proposition *</label>
                    <textarea name="proposal" rows="5" required class="w-full text-sm border border-gray-200 rounded-lg p-2.5" placeholder="Comment comptez-vous réaliser cette mission ?">{{ old('proposal') }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Tarif proposé (FCFA)</label>
                        <input type="number" min="0" name="proposed_rate" value="{{ old('proposed_rate') }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Délai de réalisation</label>
                        <input type="text" name="proposed_delay" value="{{ old('proposed_delay') }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5" placeholder="Ex: 3 jours">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Expérience</label>
                    <textarea name="experience" rows="3" class="w-full text-sm border border-gray-200 rounded-lg p-2.5" placeholder="Missions similaires déjà réalisées">{{ old('experience') }}</textarea>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Références</label>
                    <textarea name="references" rows="2" class="w-full text-sm border border-gray-200 rounded-lg p-2.5" placeholder="Clients, contacts, liens vers vos réalisations">{{ old('references') }}</textarea>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Document (CV, portfolio…)</label>
                    <input type="file" name="document" class="text-xs text-gray-500">
                    <p class="text-[10px] text-gray-400 mt-1">PDF, Word ou image — 10 Mo max. Visible uniquement par IT Holding.</p>
                </div>
                <button type="submit" class="btn-primary-gold px-8 py-3.5 text-xs font-black uppercase tracking-widest">Envoyer ma candidature</button>
            </form>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center text-sm text-gray-500">Cette mission n'accepte plus de candidatures.</div>
        @endif
    </div>
</div>
@endsection
