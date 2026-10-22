@extends('layouts.admin')
@section('title', $care->number)
@section('content')

@php
    $planCfg   = \App\Models\CareSubscription::planConfig($care->plan);
    $statusCfg = \App\Models\CareSubscription::statusConfig($care->status);
@endphp

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.care.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $care->number }}</h1>
                <span class="px-3 py-0.5 rounded-full text-sm font-bold border {{ $planCfg['classes'] }}">{{ $planCfg['icon'] }} {{ $planCfg['label'] }}</span>
                <span class="px-3 py-0.5 rounded-full text-sm font-bold border {{ $statusCfg['classes'] }}">{{ $statusCfg['label'] }}</span>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">IT HOLDING CARE+ — {{ $care->client_name }}</p>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.care.edit', $care->id) }}" class="inline-flex items-center px-4 py-2 bg-gold-500 text-navy-900 font-bold rounded-md text-sm hover:bg-gold-600 transition shadow">Modifier</a>
        <form action="{{ route('admin.care.destroy', $care->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?')">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-md text-sm hover:bg-red-700 transition shadow">Supprimer</button>
        </form>
    </div>
</div>

@if($care->status === 'active' && $care->days_remaining <= 30)
<div class="mb-5 bg-amber-50 border-l-4 border-amber-500 p-4 rounded text-amber-800 text-sm font-medium">
    ⚠️ Cet abonnement expire dans <strong>{{ $care->days_remaining }} jour(s)</strong> — le {{ $care->end_date->format('d/m/Y') }}.
    Pensez à proposer un renouvellement au client.
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- PRODUCT & CLIENT --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Client & Produit</h2>
            <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold block">Client</span>
                    @if($care->client)
                        <a href="{{ route('admin.clients.show', $care->client->id) }}" class="font-bold text-navy-700 hover:underline">{{ $care->client_name }}</a>
                    @else
                        <span class="font-bold text-gray-900">{{ $care->client_name }}</span>
                    @endif
                </div>
                @if($care->client_phone)<div><span class="text-xs text-gray-400 uppercase font-bold block">Téléphone</span><span>{{ $care->client_phone }}</span></div>@endif
                <div><span class="text-xs text-gray-400 uppercase font-bold block">Produit</span><span class="font-bold text-gray-900">{{ $care->product_name }}</span></div>
                @if($care->serial_number)<div><span class="text-xs text-gray-400 uppercase font-bold block">N° de Série</span><span class="font-mono text-navy-700">{{ $care->serial_number }}</span></div>@endif
                @if($care->warranty)<div class="col-span-2 pt-2 border-t border-gray-100 mt-1"><a href="{{ route('admin.warranties.show', $care->warranty->id) }}" class="text-xs font-bold text-green-700 hover:underline">🛡️ Garantie liée : {{ $care->warranty->number }}</a></div>@endif
            </div>
        </div>

        {{-- APPAREIL : vérification d'admissibilité --}}
        @php
            $devCfg = [
                'none'     => ['Non enregistré', 'bg-gray-100 text-gray-600'],
                'pending'  => ['À vérifier', 'bg-blue-50 text-blue-700'],
                'approved' => ['Validé', 'bg-green-50 text-green-700'],
                'rejected' => ['Refusé', 'bg-red-50 text-red-700'],
            ][$care->device_status ?: 'none'];
            $deviceCats = ['laptop' => 'Ordinateur portable', 'desktop' => 'Ordinateur fixe'];
        @endphp
        <div class="bg-white rounded-lg shadow-sm border {{ $care->device_status === 'pending' ? 'border-blue-300' : 'border-gray-100' }} p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Appareil &amp; admissibilité</h2>
                <span class="px-3 py-0.5 rounded-full text-xs font-bold {{ $devCfg[1] }}">{{ $devCfg[0] }}</span>
            </div>

            @if(($care->device_status ?: 'none') === 'none')
                <p class="text-sm text-gray-500">Le client n'a pas encore enregistré son appareil.</p>
            @else
                <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                    <div><span class="text-xs text-gray-400 uppercase font-bold block">Type</span>{{ $deviceCats[$care->device_category] ?? '—' }}</div>
                    <div><span class="text-xs text-gray-400 uppercase font-bold block">Marque / modèle</span><span class="font-bold">{{ $care->device_brand }} {{ $care->device_model }}</span></div>
                    <div><span class="text-xs text-gray-400 uppercase font-bold block">N° de série</span><span class="font-mono">{{ $care->serial_number }}</span></div>
                    <div><span class="text-xs text-gray-400 uppercase font-bold block">Date d'achat</span>{{ $care->purchase_date ? $care->purchase_date->format('d/m/Y') : 'Non renseignée' }}</div>
                    <div class="col-span-2"><span class="text-xs text-gray-400 uppercase font-bold block">État déclaré par le client</span><p class="whitespace-pre-line">{{ $care->initial_condition }}</p></div>
                    @if($care->device_submitted_at)
                        <div class="col-span-2 text-xs text-gray-400">Soumis le {{ $care->device_submitted_at->format('d/m/Y à H:i') }}</div>
                    @endif
                </div>

                @if(!empty($care->device_photos))
                    <div class="flex flex-wrap gap-2">
                        @foreach($care->device_photos as $i => $p)
                            <a href="{{ route('admin.care.device.file', [$care->id, 'photo', $i]) }}" target="_blank" class="block w-24 h-24 rounded border border-gray-200 overflow-hidden bg-gray-50">
                                <img src="{{ route('admin.care.device.file', [$care->id, 'photo', $i]) }}" class="w-full h-full object-cover" alt="Photo {{ $i + 1 }}">
                            </a>
                        @endforeach
                    </div>
                @endif
                @if($care->invoice_path)
                    <a href="{{ route('admin.care.device.file', [$care->id, 'invoice']) }}" target="_blank" class="inline-block text-sm font-bold text-navy-700 hover:underline">Voir la facture / preuve d'achat</a>
                @endif

                @if($care->device_status === 'rejected' && $care->device_rejection_reason)
                    <p class="text-sm bg-red-50 border border-red-200 text-red-700 rounded px-3 py-2">Motif communiqué au client : {{ $care->device_rejection_reason }}</p>
                @endif
                @if($care->device_reviewed_at && $care->device_status !== 'pending')
                    <p class="text-xs text-gray-400">Décision prise le {{ $care->device_reviewed_at->format('d/m/Y à H:i') }}</p>
                @endif

                @if($care->device_status === 'pending')
                    <div class="border-t border-gray-100 pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <form action="{{ route('admin.care.device.review', $care->id) }}" method="POST" onsubmit="return confirm('Valider cet appareil et délivrer le certificat ?')">
                            @csrf
                            <input type="hidden" name="decision" value="approve">
                            <button type="submit" class="w-full px-4 py-2.5 bg-green-600 text-white font-bold rounded-md text-sm hover:bg-green-700 transition">Valider l'appareil</button>
                            <p class="mt-1 text-xs text-gray-400">Le certificat devient disponible pour le client.</p>
                        </form>
                        <form action="{{ route('admin.care.device.review', $care->id) }}" method="POST" class="space-y-2">
                            @csrf
                            <input type="hidden" name="decision" value="reject">
                            <textarea name="reason" rows="2" required maxlength="500" placeholder="Motif du refus (visible par le client)" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('reason') }}</textarea>
                            @error('reason')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                            <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white font-bold rounded-md text-sm hover:bg-red-700 transition">Refuser</button>
                        </form>
                    </div>
                @endif
            @endif
        </div>

        {{-- AVANTAGES --}}
        <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg border border-purple-200 p-6">
            <h2 class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-4">Avantages du plan {{ $planCfg['label'] }}</h2>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-{{ $care->has_priority_support ? 'green' : 'gray' }}-500 text-lg">{{ $care->has_priority_support ? '✅' : '❌' }}</span>
                    <span class="font-medium {{ $care->has_priority_support ? 'text-gray-800' : 'text-gray-400 line-through' }}">Assistance prioritaire</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-{{ $care->has_home_service ? 'green' : 'gray' }}-500 text-lg">{{ $care->has_home_service ? '✅' : '❌' }}</span>
                    <span class="font-medium {{ $care->has_home_service ? 'text-gray-800' : 'text-gray-400 line-through' }}">Intervention à domicile</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-{{ $care->has_repair_discount ? 'green' : 'gray' }}-500 text-lg">{{ $care->has_repair_discount ? '✅' : '❌' }}</span>
                    <span class="font-medium {{ $care->has_repair_discount ? 'text-gray-800' : 'text-gray-400 line-through' }}">
                        Réduction réparation {{ $care->has_repair_discount ? '— '.$care->repair_discount_pct.'%' : '' }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-{{ $care->has_parts_discount ? 'green' : 'gray' }}-500 text-lg">{{ $care->has_parts_discount ? '✅' : '❌' }}</span>
                    <span class="font-medium {{ $care->has_parts_discount ? 'text-gray-800' : 'text-gray-400 line-through' }}">
                        Réduction pièces {{ $care->has_parts_discount ? '— '.$care->parts_discount_pct.'%' : '' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="space-y-5">
        {{-- Period --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-3 text-sm">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Période</h3>
            <div class="flex justify-between"><span class="text-gray-500">Début</span><span class="font-bold">{{ $care->start_date->format('d/m/Y') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Durée</span><span class="font-bold">{{ $care->duration_months }} mois</span></div>
            <div class="flex justify-between border-t pt-3"><span class="text-gray-500">Expiration</span><span class="font-bold {{ $care->days_remaining <= 30 ? 'text-amber-600' : '' }}">{{ $care->end_date->format('d/m/Y') }}</span></div>
            @if($care->status === 'active')
            <div class="bg-{{ $care->days_remaining <= 30 ? 'amber' : 'green' }}-50 rounded-md p-2 text-center">
                <span class="text-sm font-black text-{{ $care->days_remaining <= 30 ? 'amber' : 'green' }}-700">{{ $care->days_remaining }} jours restants</span>
            </div>
            @endif
        </div>

        {{-- Price --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-2 text-sm">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Facturation</h3>
            <div class="flex justify-between"><span class="text-gray-500">Montant</span><span class="font-black text-gray-900 text-base">{{ number_format($care->price, 0, ',', ' ') }} FCFA</span></div>
            @php $ps = match($care->payment_status){ 'paid'=>['Payé','bg-green-50 text-green-700'], default=>['En attente','bg-red-50 text-red-700'] }; @endphp
            <div class="text-center mt-2 py-1.5 rounded-md {{ $ps[1] }} text-xs font-bold">{{ $ps[0] }}</div>
        </div>

        @if($care->notes)
        <div class="bg-amber-50 rounded-lg border border-amber-100 p-4">
            <h3 class="text-xs font-bold text-amber-600 uppercase mb-2">Notes</h3>
            <p class="text-sm text-gray-700">{{ $care->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
