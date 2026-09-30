@extends('layouts.admin')
@section('title', 'Assistant commercial IA')
@section('content')
@php $fcfa = fn ($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA'; @endphp

<div class="mb-4">
    <h1 class="text-2xl font-bold text-gray-900">Assistant commercial IA</h1>
    <p class="text-sm text-gray-500 mt-0.5">Chaque réponse s'appuie sur les données officielles du système (ventes, stock, prix, garanties, activité du réseau). L'assistant recommande, vous décidez.</p>
</div>

@include('admin.crm._nav', ['active' => 'assistant'])

@php
    $aiBlock = function (string $section) {
        return session('ai_section') === $section && session('ai_result') ? session('ai_result') : null;
    };
@endphp

<div class="space-y-6">

    {{-- Produits à promouvoir --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-base font-bold text-gray-900">« Quels produits dois-je promouvoir cette semaine ? »</h2>
            <form action="{{ route('admin.crm.assistant.promote') }}" method="POST">@csrf
                <button type="submit" class="bg-gold-600 hover:bg-gold-700 text-white text-xs font-bold px-4 py-2 rounded">Demander la recommandation IA</button>
            </form>
        </div>
        @if($text = $aiBlock('promote'))
            <div class="bg-gold-50 border border-gold-200 rounded-lg p-4 ai-md">{!! \App\Models\CrmActivity::safeMarkdown($text) !!}</div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 text-xs">
            @foreach([
                'Meilleures ventes (30 jours)' => $promotion['best_sellers']->map(fn ($r) => $productFacts($r['product']) . ' — ' . $r['sold'] . ' vendu(s)'),
                'Stock dormant (aucune vente depuis 30 jours)' => $promotion['slow_movers']->map(fn ($p) => $productFacts($p)),
                'Promotions en cours' => $promotion['on_promo']->map(fn ($p) => $productFacts($p)),
            ] as $title => $lines)
                <div class="border border-gray-100 rounded-lg">
                    <div class="px-3 py-2 bg-gray-50 font-bold text-gray-600 uppercase text-[10px] tracking-wide">{{ $title }}</div>
                    <ul class="divide-y divide-gray-50">
                        @forelse($lines as $line)
                            <li class="px-3 py-2 text-gray-700">{{ $line }}</li>
                        @empty
                            <li class="px-3 py-2 text-gray-400 italic">Aucun produit.</li>
                        @endforelse
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Partenaires --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-base font-bold text-gray-900">« Quel partenaire génère le plus de ventes ? »</h2>
            <div class="flex gap-2">
                <form method="GET" action="{{ route('admin.crm.assistant') }}">
                    <select name="days" class="admin-select text-xs" onchange="this.form.submit()">
                        @foreach([30 => '30 derniers jours', 90 => '90 derniers jours', 365 => '12 derniers mois'] as $d => $label)
                            <option value="{{ $d }}" {{ $days === $d ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
                <form action="{{ route('admin.crm.assistant.partners') }}" method="POST">@csrf
                    <input type="hidden" name="days" value="{{ $days }}">
                    <button type="submit" class="bg-gold-600 hover:bg-gold-700 text-white text-xs font-bold px-4 py-2 rounded whitespace-nowrap" @disabled($ranking->isEmpty())>Analyse IA du réseau</button>
                </form>
            </div>
        </div>
        @if($text = $aiBlock('partners'))
            <div class="bg-gold-50 border border-gold-200 rounded-lg p-4 ai-md">{!! \App\Models\CrmActivity::safeMarkdown($text) !!}</div>
        @endif
        @if($ranking->isEmpty())
            <p class="text-sm text-gray-400 italic">Aucune vente ni opportunité partenaire sur la période.</p>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-[10px] text-gray-400 uppercase">
                    <tr>
                        <th class="text-left py-2 pr-3">#</th>
                        <th class="text-left py-2 pr-3">Partenaire</th>
                        <th class="text-right py-2 px-3">Commandes</th>
                        <th class="text-right py-2 px-3">CA généré</th>
                        <th class="text-right py-2 px-3">Commissions</th>
                        <th class="text-right py-2 pl-3">Opportunités gagnées</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($ranking->take(20) as $i => $r)
                        <tr>
                            <td class="py-2 pr-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="py-2 pr-3"><a href="{{ route('admin.professionals.show', $r['user']->id) }}" class="font-bold text-gray-900 hover:text-gold-700">{{ $r['user']->name }}</a> <span class="text-xs text-gray-400">{{ $r['user']->partner_type_label }}</span></td>
                            <td class="py-2 px-3 text-right">{{ $r['orders'] }}</td>
                            <td class="py-2 px-3 text-right font-bold">{{ $fcfa($r['revenue']) }}</td>
                            <td class="py-2 px-3 text-right text-gray-600">{{ $fcfa($r['commissions']) }}</td>
                            <td class="py-2 pl-3 text-right text-gray-600">{{ $r['deals_won'] }} · {{ $fcfa($r['deals_amount']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Profils pour une mission --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-base font-bold text-gray-900">« Trouve-moi les meilleurs profils pour cette mission »</h2>
            <form method="GET" action="{{ route('admin.crm.assistant') }}" class="flex gap-2">
                <input type="hidden" name="days" value="{{ $days }}">
                <select name="mission_id" class="admin-select text-xs" onchange="this.form.submit()">
                    <option value="">Choisir une mission…</option>
                    @foreach($missions as $m)
                        <option value="{{ $m->id }}" {{ $mission?->id === $m->id ? 'selected' : '' }}>{{ $m->reference }} — {{ $m->title }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        @if($mission)
            <ul class="divide-y divide-gray-50">
                @forelse($profiles as $s)
                    <li class="py-2 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route('admin.professionals.show', $s['user']->id) }}" class="text-sm font-bold text-gray-900 hover:text-gold-700">{{ $s['user']->name }}</a>
                            <span class="text-xs text-gray-400">{{ $s['user']->partner_type_label }}</span>
                            @if($s['applied'])<span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">a candidaté</span>@endif
                            <div class="text-[11px] text-gray-500 truncate">{{ implode(' · ', $s['reasons']) }}</div>
                        </div>
                        <span class="text-lg font-black shrink-0 {{ $s['score'] >= 60 ? 'text-green-600' : ($s['score'] >= 35 ? 'text-amber-600' : 'text-gray-400') }}">{{ $s['score'] }}<span class="text-[10px] text-gray-400">/100</span></span>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-400 italic">Aucun professionnel éligible pour cette mission.</li>
                @endforelse
            </ul>
            <a href="{{ route('admin.missions.show', $mission->id) }}" class="text-xs font-bold text-navy-700 hover:underline">Ouvrir la mission</a>
        @else
            <p class="text-sm text-gray-400">Sélectionnez une mission pour classer les freelances et prestataires selon leurs compétences, leur ville, leur vérification, leurs badges et leur disponibilité.</p>
        @endif
    </div>

    {{-- Actions sur une affaire --}}
    <div class="bg-gray-50 rounded-lg border border-gray-200 p-5 text-sm text-gray-600">
        <h2 class="text-base font-bold text-gray-900 mb-1">« Prépare une relance », « Analyse cette opportunité », « Génère un devis à partir de ce besoin »</h2>
        Ces actions se lancent depuis la fiche d'une affaire du <a href="{{ route('admin.crm.index') }}" class="font-bold text-navy-700 hover:underline">pipeline</a>, pour que l'assistant dispose du contexte complet : client, besoin, échanges, devis.
    </div>
</div>
@endsection
