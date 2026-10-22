@extends('layouts.admin')
@section('title', $claim->number)
@section('content')
@php
    $labels = \App\Models\CareClaim::LABELS;
    $next = \App\Models\CareClaim::TRANSITIONS[$claim->status] ?? [];
    $sub = $claim->subscription;
@endphp

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
        <a href="{{ route('admin.care.claims.index') }}" class="text-sm text-gray-400 hover:text-gray-700">← Pannes Care+</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $claim->number }}</h1>
        <p class="text-sm text-gray-500">{{ $sub->client_name }} · {{ $sub->product_name }}</p>
    </div>
    <span class="self-start px-3 py-1 rounded-full text-sm font-bold bg-blue-50 text-blue-700 border border-blue-200">{{ $claim->status_label }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-4 text-sm">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Déclaration du client</h2>
            <div><span class="text-xs text-gray-400 uppercase font-bold block">Symptôme</span><span class="font-bold">{{ $claim->symptoms }}</span></div>
            <div><span class="text-xs text-gray-400 uppercase font-bold block">Description</span><p class="whitespace-pre-line">{{ $claim->description }}</p></div>
            <div class="grid grid-cols-2 gap-4">
                <div><span class="text-xs text-gray-400 uppercase font-bold block">Apparu le</span>{{ $claim->started_at ? $claim->started_at->format('d/m/Y') : 'Non précisé' }}</div>
                <div><span class="text-xs text-gray-400 uppercase font-bold block">Remise</span>{{ $claim->drop_label }}</div>
                <div><span class="text-xs text-gray-400 uppercase font-bold block">Téléphone</span>{{ $claim->contact_phone }}</div>
                <div><span class="text-xs text-gray-400 uppercase font-bold block">Abonnement</span><a href="{{ route('admin.care.show', $sub->id) }}" class="font-bold text-navy-700 hover:underline">{{ $sub->number }} ({{ ucfirst($sub->plan) }})</a></div>
            </div>
            @if(!empty($claim->photos))
                <div class="flex flex-wrap gap-2">
                    @foreach($claim->photos as $i => $p)
                        <a href="{{ route('admin.care.claims.file', [$claim->id, 'photo', $i]) }}" target="_blank" class="block w-24 h-24 rounded border border-gray-200 overflow-hidden">
                            <img src="{{ route('admin.care.claims.file', [$claim->id, 'photo', $i]) }}" class="w-full h-full object-cover" alt="">
                        </a>
                    @endforeach
                </div>
            @endif
            @if($claim->video_path)
                <a href="{{ route('admin.care.claims.file', [$claim->id, 'video']) }}" target="_blank" class="font-bold text-navy-700 hover:underline">Voir la vidéo</a>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Historique</h2>
            <ul class="space-y-3 text-sm">
                @foreach($claim->events->reverse() as $event)
                    <li>
                        <span class="font-bold">{{ $labels[$event->status] ?? $event->status }}</span>
                        <span class="text-gray-400"> · {{ $event->created_at->format('d/m/Y H:i') }}{{ $event->user ? ' · ' . $event->user->name : '' }}</span>
                        @if($event->note)<p class="text-gray-600">{{ $event->note }}</p>@endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="space-y-5">
        @if($claim->is_open)
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-3">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Faire avancer le dossier</h3>
                @forelse($next as $status)
                    <form action="{{ route('admin.care.claims.status', $claim->id) }}" method="POST" class="space-y-2">
                        @csrf
                        <input type="hidden" name="status" value="{{ $status }}">
                        <input name="note" maxlength="500" placeholder="Note pour le client (facultatif)" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        <button class="w-full px-4 py-2.5 bg-green-600 text-white font-bold rounded-md text-sm hover:bg-green-700">Passer à : {{ $labels[$status] }}</button>
                    </form>
                @empty
                    <p class="text-sm text-gray-500">Le diagnostic et le devis seront gérés à l'étape suivante. Aucune autre action pour le moment.</p>
                @endforelse
            </div>

            @if(in_array($claim->status, ['received', 'checking', 'awaiting_device', 'diagnosing'], true))
                <div class="bg-white rounded-lg shadow-sm border border-red-100 p-5 space-y-2">
                    <h3 class="text-xs font-bold text-red-500 uppercase tracking-wider">Refuser la demande</h3>
                    <form action="{{ route('admin.care.claims.refuse', $claim->id) }}" method="POST" class="space-y-2" onsubmit="return confirm('Refuser cette demande ?')">
                        @csrf
                        <textarea name="reason" rows="3" required maxlength="500" placeholder="Motif (visible par le client)" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></textarea>
                        @error('reason')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        <button class="w-full px-4 py-2.5 bg-red-600 text-white font-bold rounded-md text-sm hover:bg-red-700">Refuser</button>
                    </form>
                </div>
            @endif
        @else
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-5 text-sm text-gray-500">Ce dossier est terminé. @if($claim->refusal_reason)<br>Motif : {{ $claim->refusal_reason }}@endif</div>
        @endif
    </div>
</div>
@endsection
