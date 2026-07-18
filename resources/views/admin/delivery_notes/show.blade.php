@extends('layouts.admin')

@section('title', 'Bon de livraison ' . $deliveryNote->number)

@section('content')
@if(isset($print) && $print)
<style>
    body { background-color: white !important; color: black !important; }
    aside, nav, header, .no-print, button, a { display: none !important; }
    .print-container { width: 100% !important; margin: 0 !important; padding: 0 !important; box-shadow: none !important; border: none !important; }
</style>
<script>window.onload = function() { window.print(); }</script>
@endif

{{-- TOP BAR (non-print) --}}
<div class="mb-6 flex flex-col md:flex-row md:items-start md:justify-between gap-4 no-print">
    <div>
        <a href="{{ route('admin.delivery-notes.index') }}" class="text-navy-600 hover:text-navy-900 font-medium flex items-center gap-1 mb-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Retour à la liste
        </a>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            {{ $deliveryNote->number }}
            @if($deliveryNote->type === 'envoi')
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 border border-blue-200 text-blue-700">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    Envoi Client
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 border border-green-200 text-green-700">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                    Réception Fournisseur
                </span>
            @endif
        </h1>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.delivery-notes.edit', $deliveryNote->id) }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white font-bold rounded-md text-sm hover:bg-navy-700 transition shadow">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Modifier
        </a>
        <a href="{{ route('admin.delivery-notes.print', $deliveryNote->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gold-500 text-navy-900 font-bold rounded-md text-sm hover:bg-gold-600 transition shadow">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Imprimer
        </a>
        <form action="{{ route('admin.delivery-notes.destroy', $deliveryNote->id) }}" method="POST" onsubmit="return confirm('Supprimer ce bon et réajuster les stocks ?')">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-bold rounded-md text-sm hover:bg-red-700 transition shadow">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Supprimer
            </button>
        </form>
    </div>
</div>

{{-- STATUS STEPPER (non-print) --}}
<div class="no-print mb-6 bg-white rounded-lg shadow-sm border border-gray-100 px-8 py-5">
    @php
        $isEnvoi = $deliveryNote->type === 'envoi';
        $currentStatus = $deliveryNote->status;
        $steps = $isEnvoi
            ? [
                ['key' => 'draft',     'label' => 'Brouillon',  'icon' => '📝'],
                ['key' => 'pending',   'label' => 'En attente', 'icon' => '⏳'],
                ['key' => 'shipped',   'label' => 'Expédié',    'icon' => '🚚'],
                ['key' => 'delivered', 'label' => 'Livré',      'icon' => '✅'],
              ]
            : [
                ['key' => 'draft',    'label' => 'Brouillon', 'icon' => '📝'],
                ['key' => 'received', 'label' => 'Reçu',      'icon' => '📦'],
              ];
        $statusOrder = array_column($steps, 'key');
        $currentIndex = array_search($currentStatus, $statusOrder) ?? 0;
    @endphp
    <div class="flex items-center">
        @foreach($steps as $i => $step)
            @php $done = $i <= $currentIndex; @endphp
            <div class="flex flex-col items-center flex-1">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-base font-bold border-2 transition
                    {{ $done ? ($isEnvoi ? 'bg-blue-500 border-blue-500 text-white' : 'bg-green-500 border-green-500 text-white') : 'bg-white border-gray-300 text-gray-400' }}">
                    {{ $step['icon'] }}
                </div>
                <span class="mt-1 text-xs font-semibold {{ $done ? 'text-navy-700' : 'text-gray-400' }}">{{ $step['label'] }}</span>
            </div>
            @if(!$loop->last)
                <div class="flex-1 h-1 rounded {{ $i < $currentIndex ? ($isEnvoi ? 'bg-blue-400' : 'bg-green-400') : 'bg-gray-200' }}"></div>
            @endif
        @endforeach
    </div>
</div>

{{-- PRINT DOCUMENT --}}
<div class="print-container bg-white shadow-sm rounded-lg border border-gray-150 overflow-hidden max-w-4xl mx-auto p-8 md:p-12">
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b pb-6 mb-8 gap-4">
        <div>
            <div class="text-3xl font-black text-navy-900 tracking-wider">IT-HOLDING</div>
            <div class="text-sm text-gray-500 mt-1">Solutions Informatiques & Services</div>
        </div>
        <div class="text-right">
            <div class="text-2xl font-black text-navy-900">BON DE LIVRAISON</div>
            <div class="text-lg font-bold text-gold-600 mt-1">{{ $deliveryNote->number }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ $deliveryNote->delivery_date->format('d/m/Y') }}</div>
            <div class="mt-2">
                @php
                    $statusConfig = match($deliveryNote->status) {
                        'draft'     => ['label' => 'Brouillon',   'classes' => 'bg-gray-100 text-gray-700 border-gray-200'],
                        'pending'   => ['label' => 'En attente',  'classes' => 'bg-amber-50 text-amber-700 border-amber-200'],
                        'shipped'   => ['label' => 'Expédié',     'classes' => 'bg-blue-50 text-blue-700 border-blue-200'],
                        'delivered' => ['label' => 'Livré',       'classes' => 'bg-green-50 text-green-700 border-green-200'],
                        'received'  => ['label' => 'Reçu',        'classes' => 'bg-green-50 text-green-700 border-green-200'],
                        default     => ['label' => ucfirst($deliveryNote->status), 'classes' => 'bg-gray-100 text-gray-700 border-gray-200'],
                    };
                @endphp
                <span class="px-3 py-0.5 rounded-full text-xs font-bold border {{ $statusConfig['classes'] }}">{{ $statusConfig['label'] }}</span>
            </div>
        </div>
    </div>

    {{-- COUNTERPARTY BLOCK --}}
    <div class="grid grid-cols-2 gap-8 mb-8">
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Expéditeur</p>
            <p class="font-bold text-gray-900">IT-HOLDING Services</p>
            <p class="text-sm text-gray-500">Dakar, Sénégal</p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                {{ $deliveryNote->type === 'envoi' ? 'Destinataire (Client)' : 'Fournisseur' }}
            </p>
            @if($deliveryNote->type === 'envoi')
                @if($deliveryNote->client)
                    <p class="font-bold text-navy-700 hover:underline no-print">
                        <a href="{{ route('admin.clients.show', $deliveryNote->client_id) }}">
                            {{ $deliveryNote->customer_name }}
                        </a>
                    </p>
                    <p class="font-bold text-gray-900 hidden print:block">{{ $deliveryNote->customer_name }}</p>
                @else
                    <p class="font-bold text-gray-900">{{ $deliveryNote->customer_name ?? '—' }}</p>
                @endif
                @if($deliveryNote->customer_phone)
                    <p class="text-sm text-gray-500">{{ $deliveryNote->customer_phone }}</p>
                @endif
                @if($deliveryNote->customer_address)
                    <p class="text-sm text-gray-500">{{ $deliveryNote->customer_address }}</p>
                @endif
                @if($deliveryNote->order)
                    <p class="text-xs text-blue-600 mt-1 font-semibold">→ Commande #{{ $deliveryNote->order_id }}</p>
                @endif
                @if($deliveryNote->invoice)
                    <p class="text-xs text-purple-600 mt-1 font-semibold">→ Facture #{{ $deliveryNote->invoice_id }}</p>
                @endif
            @else
                @if($deliveryNote->supplier)
                    <a href="{{ route('admin.suppliers.show', $deliveryNote->supplier->id) }}" class="font-bold text-navy-700 hover:underline no-print">{{ $deliveryNote->supplier_name }}</a>
                    <p class="font-bold text-navy-700 hidden print:block">{{ $deliveryNote->supplier_name }}</p>
                @else
                    <p class="font-bold text-gray-900">{{ $deliveryNote->supplier_name ?? '—' }}</p>
                @endif
            @endif
        </div>
    </div>

    {{-- ITEMS TABLE --}}
    <table class="w-full mb-8 border-collapse">
        <thead>
            <tr class="bg-navy-600 text-white">
                <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider rounded-tl">#</th>
                <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider">Désignation</th>
                <th class="text-center px-4 py-3 text-xs font-bold uppercase tracking-wider">Qté</th>
                <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider">{{ $deliveryNote->type === 'reception' ? 'P.U. Achat' : 'P.U. Vente' }}</th>
                <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider rounded-tr">Total H.T.</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($deliveryNote->items as $i => $item)
            <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                <td class="px-4 py-3 text-sm text-gray-400">{{ $i + 1 }}</td>
                <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $item->product_name }}</td>
                <td class="px-4 py-3 text-sm text-center font-bold text-gray-900">{{ number_format($item->quantity, 0) }}</td>
                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($item->purchase_price, 0, ',', ' ') }} FCFA</td>
                <td class="px-4 py-3 text-sm text-right font-bold text-navy-900">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-navy-900">
                <td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-gray-700 uppercase">Montant Total</td>
                <td class="px-4 py-3 text-right text-lg font-black text-navy-900">{{ number_format($deliveryNote->total_purchase_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tfoot>
    </table>

    {{-- NOTES --}}
    @if($deliveryNote->notes)
    <div class="border-t border-gray-100 pt-6 mb-6">
        <p class="text-xs font-bold text-gray-400 uppercase mb-2">Observations</p>
        <p class="text-sm text-gray-700">{{ $deliveryNote->notes }}</p>
    </div>
    @endif

    {{-- SIGNATURES --}}
    <div class="grid grid-cols-2 gap-8 mt-8 pt-6 border-t border-dashed border-gray-200">
        <div class="text-center flex flex-col items-center">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Émetteur</p>
            <div class="h-24 flex items-center justify-center mb-2">
                @if(file_exists(public_path('signature_cachet.png')))
                    <img src="{{ asset('signature_cachet.png') }}" alt="Signature & Cachet" class="max-h-24 object-contain">
                @elseif(file_exists(public_path('signature.png')) || file_exists(public_path('cachet.png')))
                    <div class="flex justify-center gap-2">
                        @if(file_exists(public_path('signature.png')))
                            <img src="{{ asset('signature.png') }}" alt="Signature" class="max-h-24 object-contain">
                        @endif
                        @if(file_exists(public_path('cachet.png')))
                            <img src="{{ asset('cachet.png') }}" alt="Cachet" class="max-h-24 object-contain">
                        @endif
                    </div>
                @else
                    <span class="text-gray-300 text-xs italic">Placez signature.png et/ou cachet.png dans public/</span>
                @endif
            </div>
            <div class="w-full border-t border-gray-400 pt-2 text-sm text-gray-500">Signature & Cachet IT-HOLDING</div>
        </div>
        <div class="text-center flex flex-col items-center">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">
                {{ $deliveryNote->type === 'envoi' ? 'Destinataire' : 'Réceptionné par' }}
            </p>
            <div class="h-24 flex items-center justify-center text-gray-400 text-xs italic mb-2">
                Nom, signature et date
            </div>
            <div class="w-full border-t border-gray-400 pt-2 text-sm text-gray-500">Signature & Date Client</div>
        </div>
    </div>
</div>
@endsection
