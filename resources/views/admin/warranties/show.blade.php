@extends('layouts.admin')
@section('title', 'Garantie ' . $warranty->number)
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.warranties.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $warranty->number }}</h1>
            <p class="text-sm text-gray-500">{{ \App\Models\Warranty::typeLabel($warranty->type) }}</p>
        </div>
        @php $cfg = \App\Models\Warranty::statusConfig($warranty->status); @endphp
        <span class="px-3 py-0.5 rounded-full text-sm font-bold border {{ $cfg['classes'] }}">{{ $cfg['label'] }}</span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.warranties.edit', $warranty->id) }}" class="inline-flex items-center px-4 py-2 bg-gold-500 text-navy-900 font-bold rounded-md text-sm hover:bg-gold-600 transition shadow gap-2">Modifier</a>
        <form action="{{ route('admin.warranties.destroy', $warranty->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette garantie ?')">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-bold rounded-md text-sm hover:bg-red-700 transition shadow">Supprimer</button>
        </form>
    </div>
</div>

{{-- EXPIRY ALERT --}}
@if($warranty->status === 'active')
    @if($warranty->is_expired)
        <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded text-red-800 text-sm font-medium">⛔ Cette garantie est <strong>expirée</strong> depuis le {{ $warranty->expiry_date->format('d/m/Y') }}. Mettez le statut à jour.</div>
    @elseif($warranty->days_remaining <= 30)
        <div class="mb-5 bg-amber-50 border-l-4 border-amber-500 p-4 rounded text-amber-800 text-sm font-medium">⚠️ Cette garantie expire dans <strong>{{ $warranty->days_remaining }} jour(s)</strong> — le {{ $warranty->expiry_date->format('d/m/Y') }}.</div>
    @endif
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- PRODUCT --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Produit Couvert</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-400 block text-xs">Désignation</span><span class="font-bold text-gray-900">{{ $warranty->product_name }}</span></div>
                @if($warranty->serial_number)
                <div><span class="text-gray-400 block text-xs">N° de Série</span><span class="font-mono font-bold text-navy-700">{{ $warranty->serial_number }}</span></div>
                @endif
                @if($warranty->product)
                <div><span class="text-gray-400 block text-xs">Produit lié</span><a href="{{ route('admin.products.show', $warranty->product->id) }}" class="text-navy-600 hover:underline font-semibold">{{ $warranty->product->name }}</a></div>
                @endif
                @if($warranty->invoice)
                <div><span class="text-gray-400 block text-xs">Facture d'origine</span><a href="{{ route('admin.invoices.show', $warranty->invoice->id) }}" class="text-navy-600 hover:underline font-semibold">{{ $warranty->invoice->number }}</a></div>
                @endif
            </div>
        </div>

        {{-- CLIENT --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Client</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-400 block text-xs">Nom</span>
                    @if($warranty->client)
                        <a href="{{ route('admin.clients.show', $warranty->client->id) }}" class="font-bold text-navy-700 hover:underline">{{ $warranty->client_name }}</a>
                    @else
                        <span class="font-bold text-gray-900">{{ $warranty->client_name }}</span>
                    @endif
                </div>
                @if($warranty->client_phone)
                <div><span class="text-gray-400 block text-xs">Téléphone</span><span class="font-semibold text-gray-900">{{ $warranty->client_phone }}</span></div>
                @endif
            </div>
        </div>

        {{-- COVERAGE --}}
        @if($warranty->coverage_notes || $warranty->exclusions)
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Couverture</h2>
            @if($warranty->coverage_notes)
            <div>
                <p class="text-xs font-bold text-green-600 uppercase mb-1">✅ Ce qui est couvert</p>
                <p class="text-sm text-gray-700">{{ $warranty->coverage_notes }}</p>
            </div>
            @endif
            @if($warranty->exclusions)
            <div>
                <p class="text-xs font-bold text-red-500 uppercase mb-1">❌ Exclusions</p>
                <p class="text-sm text-gray-700">{{ $warranty->exclusions }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- SIDEBAR --}}
    <div class="space-y-5">
        {{-- Timeline --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Période de garantie</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Date d'achat</span><span class="font-bold">{{ $warranty->purchase_date->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Durée</span><span class="font-bold">{{ $warranty->duration_months }} mois</span></div>
                <div class="flex justify-between border-t pt-3"><span class="text-gray-500">Expiration</span><span class="font-bold {{ $warranty->days_remaining <= 30 && $warranty->status === 'active' ? 'text-amber-600' : '' }}">{{ $warranty->expiry_date->format('d/m/Y') }}</span></div>
                @if($warranty->status === 'active' && !$warranty->is_expired)
                <div class="bg-green-50 rounded-md p-2 text-center">
                    <span class="text-sm font-black text-green-700">{{ $warranty->days_remaining }} jours restants</span>
                </div>
                @endif
            </div>
        </div>

        @if($warranty->notes)
        <div class="bg-amber-50 rounded-lg border border-amber-100 p-4">
            <h3 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-2">Notes</h3>
            <p class="text-sm text-gray-700">{{ $warranty->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
