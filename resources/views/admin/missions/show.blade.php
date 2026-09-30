@extends('layouts.admin')
@section('title', $mission->title)
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.missions.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $mission->title }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                <span class="font-mono">{{ $mission->reference }}</span>
                @if($mission->project_ref) · Projet <span class="font-mono">{{ $mission->project_ref }}</span>@endif
                · {{ \App\Models\Mission::STATUSES[$mission->status] }}
            </p>
        </div>
    </div>
    <div class="flex gap-2">
        @if($mission->hasWorkspace())
            <a href="{{ route('admin.missions.workspace', $mission->id) }}" class="inline-flex items-center px-4 py-2 bg-gold-600 text-white rounded-md font-bold text-sm hover:bg-gold-700 transition shadow-sm">Espace projet</a>
        @endif
        <a href="{{ route('admin.missions.edit', $mission->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-navy-700 rounded-md font-bold text-sm hover:bg-gray-50 transition shadow-sm">Modifier</a>
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-3 rounded text-sm text-red-800 font-medium">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- Applications --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide">Candidatures ({{ $mission->applications->count() }})</h2>
                <span class="text-xs text-gray-400">{{ $mission->applications->filter->isTeamMember()->count() }}/{{ $mission->positions }} retenu(s)</span>
            </div>

            @forelse($mission->applications as $app)
            <div class="p-6 border-b border-gray-100 last:border-0" x-data="{ open: false }">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-bold text-gray-900">{{ $app->user->name }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $app->status_classes }}">{{ $app->status_label }}</span>
                            @if($app->user->professionalProfile)
                                <a href="{{ route('admin.professionals.show', $app->user_id) }}" class="text-[11px] font-mono text-gold-700 hover:underline">{{ $app->user->professionalProfile->pro_id }} · vérif. {{ $app->user->professionalProfile->verification_level }}/5</a>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $app->user->partner_type_label }}
                            @if($app->user->professionalProfile?->isStructure()) · <strong>{{ $app->user->professionalProfile->company_name }}</strong>@endif
                            @if($app->proposed_rate) · Tarif proposé : <strong>{{ number_format($app->proposed_rate, 0, ',', ' ') }} FCFA</strong>@endif
                            @if($app->proposed_delay) · Délai : {{ $app->proposed_delay }}@endif
                        </div>
                    </div>
                    <button type="button" @click="open = !open" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-3 py-1.5 rounded transition shrink-0" x-text="open ? 'Fermer' : 'Examiner'"></button>
                </div>

                <div x-show="open" x-cloak class="mt-4 space-y-4">
                    <div class="text-sm text-gray-700 whitespace-pre-line bg-gray-50 rounded p-3 border border-gray-100">{{ $app->proposal }}</div>
                    @if($app->experience)
                        <div class="text-xs"><span class="font-bold text-gray-500 uppercase">Expérience :</span> <span class="text-gray-700 whitespace-pre-line">{{ $app->experience }}</span></div>
                    @endif
                    @if($app->references)
                        <div class="text-xs"><span class="font-bold text-gray-500 uppercase">Références :</span> <span class="text-gray-700 whitespace-pre-line">{{ $app->references }}</span></div>
                    @endif
                    @if($app->document_path)
                        <a href="{{ route('admin.missions.applications.document', $app->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-navy-600 hover:underline">📎 {{ $app->document_name }}</a>
                    @endif

                    @if($app->status === 'paid')
                        <div class="bg-green-50 border border-green-200 rounded p-3 text-xs text-green-800">
                            Payé le {{ $app->paid_at->format('d/m/Y') }} — convenu {{ number_format($app->agreed_amount, 0, ',', ' ') }} FCFA,
                            retenue {{ number_format($app->withholding_amount, 0, ',', ' ') }} FCFA,
                            <strong>net versé {{ number_format($app->net_amount, 0, ',', ' ') }} FCFA</strong>.
                        </div>
                    @else
                        <form action="{{ route('admin.missions.applications.update', $app->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-3 border-t border-gray-100 pt-4">
                            @csrf @method('PUT')
                            <div>
                                <label class="admin-label">Étape</label>
                                <select name="status" class="admin-select">
                                    @foreach(\App\Models\MissionApplication::STATUSES as $key => $label)
                                        <option value="{{ $key }}" {{ $app->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="admin-label">Montant convenu (FCFA)</label>
                                <input type="number" step="1" min="0" name="agreed_amount" value="{{ $app->agreed_amount }}" class="admin-input" placeholder="Requis pour « Payé »">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="admin-label">Commentaire sur ce changement (historique)</label>
                                <input type="text" name="note" class="admin-input" placeholder="Ex: entretien réalisé le 02/10">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="admin-label">Notes internes</label>
                                <textarea name="admin_notes" rows="2" class="admin-textarea">{{ $app->admin_notes }}</textarea>
                            </div>
                            <div class="sm:col-span-2">
                                <button type="submit" class="bg-navy-600 text-white font-bold px-5 py-2 rounded-md hover:bg-navy-700 transition text-sm">Enregistrer</button>
                                <span class="text-[11px] text-gray-400 ml-2">« Payé » applique la retenue à la source configurée et crédite le portefeuille du professionnel.</span>
                            </div>
                        </form>
                    @endif

                    @if(!empty($app->status_history))
                        <ul class="border-t border-gray-100 pt-3 space-y-1 text-[11px] text-gray-500">
                            @foreach($app->status_history as $h)
                                <li>{{ $h['at'] }} — {{ $h['from'] ? (\App\Models\MissionApplication::STATUSES[$h['from']] ?? $h['from']) . ' → ' : '' }}<strong>{{ \App\Models\MissionApplication::STATUSES[$h['to']] ?? $h['to'] }}</strong> ({{ $h['by'] }}){{ !empty($h['note']) ? ' : ' . $h['note'] : '' }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            @empty
                <div class="p-8 text-center text-sm text-gray-400">
                    @if($mission->status === 'open')
                        Aucune candidature pour l'instant.
                    @else
                        Aucune candidature. Passez la mission en « Ouverte aux candidatures » pour la publier.
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Suggestions --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide">Profils suggérés</h2>
                <p class="text-xs text-gray-400 mt-1">Classement indicatif (compétences, ville, vérification, badges, disponibilité). Le système recommande, vous décidez.</p>
            </div>
            @forelse($suggestions as $s)
                <div class="px-6 py-3 border-b border-gray-50 last:border-0 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <a href="{{ route('admin.professionals.show', $s['user']->id) }}" class="text-sm font-bold text-gray-900 hover:text-gold-700">{{ $s['user']->name }}</a>
                        <span class="text-xs text-gray-400">{{ $s['user']->partner_type_label }}@if($s['user']->professionalProfile?->isStructure()) · {{ $s['user']->professionalProfile->company_name }}{{ $s['user']->professionalProfile->team_size ? ' (' . $s['user']->professionalProfile->team_size . ' pers.)' : '' }}@endif</span>
                        @if($s['applied'])<span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">a candidaté</span>@endif
                        <div class="text-[11px] text-gray-500 truncate">{{ implode(' · ', $s['reasons']) }}</div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-lg font-black {{ $s['score'] >= 60 ? 'text-green-600' : ($s['score'] >= 35 ? 'text-amber-600' : 'text-gray-400') }}">{{ $s['score'] }}</div>
                        <div class="text-[10px] text-gray-400">/100</div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-sm text-gray-400">Aucun freelance ou prestataire approuvé dans le réseau pour l'instant.</div>
            @endforelse
        </div>
    </div>

    {{-- Mission details --}}
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-3 text-sm">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide">Détails</h2>
            <div class="text-gray-700 whitespace-pre-line">{{ $mission->description }}</div>
            <dl class="grid grid-cols-2 gap-3 border-t border-gray-100 pt-3 text-xs">
                <div><dt class="text-gray-400">Ville</dt><dd class="font-bold text-gray-800">{{ $mission->city ?: '—' }}</dd></div>
                <div><dt class="text-gray-400">Budget</dt><dd class="font-bold text-gray-800">{{ $mission->budget ? number_format($mission->budget, 0, ',', ' ') . ' FCFA' : '—' }}</dd></div>
                <div><dt class="text-gray-400">Durée</dt><dd class="font-bold text-gray-800">{{ $mission->duration ?: '—' }}</dd></div>
                <div><dt class="text-gray-400">Début</dt><dd class="font-bold text-gray-800">{{ $mission->start_date?->format('d/m/Y') ?? '—' }}</dd></div>
                <div><dt class="text-gray-400">Candidatures jusqu'au</dt><dd class="font-bold text-gray-800">{{ $mission->apply_until?->format('d/m/Y') ?? '—' }}</dd></div>
                <div><dt class="text-gray-400">Ouverte à</dt><dd class="font-bold text-gray-800">{{ $mission->target_label }}</dd></div>
                <div><dt class="text-gray-400">Client</dt><dd class="font-bold text-gray-800">{{ $mission->client ? ($mission->client->company_name ?: trim($mission->client->first_name . ' ' . $mission->client->last_name)) : 'Interne' }}</dd></div>
            </dl>
            @if($mission->required_skills)
                <div class="flex flex-wrap gap-1 border-t border-gray-100 pt-3">
                    @foreach($mission->required_skills as $skill)
                        <span class="px-2 py-0.5 rounded bg-gray-100 text-[11px] font-bold text-gray-700">{{ $skill }}</span>
                    @endforeach
                </div>
            @endif
            @if($mission->equipment)
                <div class="text-xs border-t border-gray-100 pt-3"><span class="text-gray-400">Matériel :</span> {{ $mission->equipment }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
