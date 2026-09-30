@extends('layouts.admin')
@section('title', $deal->title)
@section('content')
@php $fcfa = fn ($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA'; @endphp

<div class="mb-4 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
    <div class="flex items-start gap-3">
        <a href="{{ route('admin.crm.index') }}" class="text-gray-400 hover:text-gray-700 mt-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $deal->title }}</h1>
            <p class="text-sm text-gray-500 mt-0.5 flex flex-wrap items-center gap-2">
                <span class="font-mono">{{ $deal->reference }}</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold border {{ $deal->stage_classes }}">{{ $deal->stage_label }}</span>
                <span>{{ $deal->display_name }}</span>
                @if($deal->amount)<span class="font-bold text-navy-900">{{ $fcfa($deal->amount) }}</span>@endif
            </p>
        </div>
    </div>
    <a href="{{ route('admin.crm.edit', $deal->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-navy-700 rounded-md font-bold text-sm hover:bg-gray-50 transition shadow-sm shrink-0">Modifier</a>
</div>

@include('admin.crm._nav', ['active' => 'pipeline'])

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- Étape --}}
        <form action="{{ route('admin.crm.stage', $deal->id) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4" x-data="{ stage: @js($deal->stage) }">
            @csrf
            <div class="flex flex-wrap gap-1">
                @foreach(\App\Models\CrmDeal::STAGES as $key => $label)
                    <button type="button" @click="stage = @js($key)" :class="stage === @js($key) ? '{{ $key === 'lost' ? 'bg-red-600' : 'bg-navy-600' }} text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded text-xs font-bold transition">{{ $label }}</button>
                @endforeach
            </div>
            <input type="hidden" name="stage" :value="stage">
            <div class="flex flex-wrap items-center gap-2 mt-3" x-show="stage !== @js($deal->stage)" x-cloak>
                <input type="text" name="lost_reason" x-show="stage === 'lost'" class="admin-input max-w-sm" placeholder="Motif de perte (obligatoire)">
                <button type="submit" class="bg-navy-600 text-white text-xs font-bold px-4 py-2 rounded hover:bg-navy-700">Passer à cette étape</button>
            </div>
        </form>

        {{-- Assistant IA (doc §44-45) --}}
        <div class="bg-white rounded-lg shadow-sm border border-gold-200 p-5 space-y-4">
            <div>
                <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide">Assistant commercial IA</h2>
                <p class="text-xs text-gray-500 mt-1">L'assistant s'appuie uniquement sur les données de l'affaire et du catalogue officiel. Relisez toujours avant d'envoyer au client.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form action="{{ route('admin.crm.ai.follow-up', $deal->id) }}" method="POST">@csrf<input type="hidden" name="channel" value="whatsapp">
                    <button type="submit" class="text-xs font-bold bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 px-3 py-2 rounded">Relance WhatsApp</button></form>
                <form action="{{ route('admin.crm.ai.follow-up', $deal->id) }}" method="POST">@csrf<input type="hidden" name="channel" value="email">
                    <button type="submit" class="text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 px-3 py-2 rounded">Relance e-mail</button></form>
                <form action="{{ route('admin.crm.ai.analyze', $deal->id) }}" method="POST">@csrf
                    <button type="submit" class="text-xs font-bold bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 px-3 py-2 rounded">Analyser l'opportunité</button></form>
                <form action="{{ route('admin.crm.ai.quote', $deal->id) }}" method="POST" onsubmit="return confirm('Créer un brouillon de devis à partir du besoin ? Les prix seront ceux du catalogue.')">@csrf
                    <button type="submit" class="text-xs font-bold bg-gold-50 text-gold-800 hover:bg-gold-100 border border-gold-200 px-3 py-2 rounded disabled:opacity-50" @disabled(blank($deal->need)) @if(blank($deal->need)) title="Renseignez d'abord le besoin du client (Modifier)" @endif>Générer un devis depuis le besoin</button></form>
            </div>

            @if(session('ai_result'))
                <div x-data="{ copied: false }" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="ai-md" x-ref="ai">{!! \App\Models\CrmActivity::safeMarkdown(session('ai_result')) !!}</div>
                    <button type="button" @click="navigator.clipboard.writeText($refs.ai.innerText); copied = true; setTimeout(() => copied = false, 2000)" class="mt-3 text-xs font-bold text-navy-700 hover:underline" x-text="copied ? 'Copié ✓' : 'Copier le texte'"></button>
                </div>
            @endif
        </div>

        {{-- Journal --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <form action="{{ route('admin.crm.activities.store', $deal->id) }}" method="POST" class="p-5 border-b border-gray-100 space-y-3">
                @csrf
                <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide">Nouvel échange</h2>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <select name="type" class="admin-select">
                        @foreach(\App\Models\CrmDeal::ACTIVITY_TYPES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <textarea name="body" rows="2" required class="admin-textarea sm:col-span-3" placeholder="Compte rendu de l'échange…"></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    <div>
                        <label class="admin-label">Prochaine relance</label>
                        <input type="datetime-local" name="next_action_at" class="admin-input" value="{{ optional($deal->next_action_at)->format('Y-m-d\TH:i') }}">
                    </div>
                    <div>
                        <label class="admin-label">Action prévue</label>
                        <input type="text" name="next_action_note" class="admin-input" value="{{ $deal->next_action_note }}">
                    </div>
                    <button type="submit" class="bg-navy-600 text-white font-bold px-4 py-2 rounded-md hover:bg-navy-700 text-sm">Enregistrer</button>
                </div>
            </form>
            <ul class="divide-y divide-gray-50">
                @forelse($deal->activities as $a)
                    <li class="px-5 py-3 text-sm">
                        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                            <span class="font-bold {{ $a->type === 'ai' ? 'text-gold-700' : 'text-gray-600' }}">{{ $a->type_label }}</span>
                            <span>{{ $a->created_at->format('d/m/Y H:i') }}</span>
                            <span>· {{ $a->user?->name ?? 'Système' }}</span>
                        </div>
                        @if($a->type === 'ai')
                            <details class="text-gray-700">
                                <summary class="cursor-pointer text-gray-800 font-medium">{{ \Illuminate\Support\Str::before($a->body, "\n") }}</summary>
                                <div class="ai-md mt-2">{!! \App\Models\CrmActivity::safeMarkdown(\Illuminate\Support\Str::after($a->body, "\n")) !!}</div>
                            </details>
                        @else
                            <div class="text-gray-700 whitespace-pre-line">{{ $a->body }}</div>
                        @endif
                    </li>
                @empty
                    <li class="px-5 py-6 text-center text-sm text-gray-400">Aucun échange enregistré.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Détails --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-3 text-sm">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide">Détails</h2>
            <dl class="space-y-2 text-xs">
                <div><dt class="text-gray-400">Client</dt><dd class="font-bold text-gray-800">
                    @if($deal->client)<a href="{{ route('admin.clients.show', $deal->client_id) }}" class="hover:text-gold-700">{{ $deal->display_name }}</a>@else{{ $deal->display_name }} <span class="font-normal text-gray-400">(prospect)</span>@endif
                </dd></div>
                @if($deal->contact_name || $deal->contact_phone || $deal->contact_email)
                <div><dt class="text-gray-400">Contact</dt><dd class="text-gray-800">{{ $deal->contact_name }} {{ $deal->contact_phone }} {{ $deal->contact_email }}</dd></div>
                @endif
                <div><dt class="text-gray-400">Source</dt><dd class="text-gray-800">{{ \App\Models\CrmDeal::SOURCES[$deal->source] ?? $deal->source }}
                    @if($deal->partner) — <a href="{{ route('admin.professionals.show', $deal->partner_id) }}" class="font-bold text-purple-700 hover:underline">{{ $deal->partner->name }}</a>@endif
                    @if($deal->partnerProspect?->entry_type === 'opportunity') (<a href="{{ route('admin.opportunities.show', $deal->partner_prospect_id) }}" class="hover:underline">{{ $deal->partnerProspect->opportunity_ref }}</a>)@endif
                </dd></div>
                <div><dt class="text-gray-400">Commercial</dt><dd class="font-bold text-gray-800">{{ $deal->owner?->name ?? 'Non attribuée' }}</dd></div>
                <div><dt class="text-gray-400">Prochaine relance</dt><dd class="{{ $deal->isFollowUpLate() ? 'text-red-600 font-bold' : 'text-gray-800' }}">{{ $deal->next_action_at ? $deal->next_action_at->format('d/m/Y H:i') . ' — ' . ($deal->next_action_note ?: 'Relance') : '—' }}</dd></div>
                @if($deal->lost_reason)<div><dt class="text-gray-400">Motif de perte</dt><dd class="text-red-700">{{ $deal->lost_reason }}</dd></div>@endif
            </dl>
            @if($deal->need)
                <div class="border-t border-gray-100 pt-3 text-xs"><span class="text-gray-400 block mb-1">Besoin</span><span class="text-gray-700 whitespace-pre-line">{{ $deal->need }}</span></div>
            @endif
            @if($deal->partnerProspect?->entry_type === 'opportunity')
                <p class="text-[11px] text-purple-700 bg-purple-50 border border-purple-100 rounded p-2">Affaire apportée par un apporteur : quand elle est conclue, passez aussi l'opportunité {{ $deal->partnerProspect->opportunity_ref }} à « Gagné » pour verser sa commission.</p>
            @endif
        </div>

        {{-- Devis --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-3">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide">Devis</h2>
            @if($deal->quote)
                <a href="{{ route('admin.quotes.show', $deal->quote_id) }}" class="block border border-gray-200 rounded p-3 hover:border-gold-300">
                    <span class="font-mono text-sm font-bold text-gray-900">{{ $deal->quote->number }}</span>
                    <span class="text-xs text-gray-500">· {{ $deal->quote->status }}</span>
                    <span class="block text-sm font-black text-navy-900">{{ $fcfa($deal->quote->total_amount) }}</span>
                </a>
                <a href="{{ route('admin.quotes.edit', $deal->quote_id) }}" class="text-xs font-bold text-navy-700 hover:underline">Modifier le devis</a>
            @endif
            <form action="{{ route('admin.crm.quote.link', $deal->id) }}" method="POST" class="flex gap-2">
                @csrf
                <select name="quote_id" class="admin-select text-xs" required>
                    <option value="">Associer un devis existant…</option>
                    @foreach($quotes as $q)
                        <option value="{{ $q->id }}">{{ $q->number }} — {{ $q->client_name }} ({{ number_format($q->total_amount, 0, ',', ' ') }})</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-xs font-bold px-3 rounded">OK</button>
            </form>
            <a href="{{ route('admin.quotes.create') }}" class="text-xs font-bold text-gold-700 hover:underline">+ Créer un devis manuellement</a>
        </div>
    </div>
</div>
@endsection
