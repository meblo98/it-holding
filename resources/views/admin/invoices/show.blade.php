@extends('layouts.admin')

@section('title', 'Détails de la Facture ' . $invoice->number)

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Facture {{ $invoice->number }}</h1>
        <p class="text-gray-500">Créée le {{ $invoice->created_at->format('d/m/Y') }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.invoices.print', $invoice->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Imprimer
        </a>
        <a href="{{ route('admin.invoices.share', $invoice->id) }}" class="inline-flex items-center px-4 py-2 bg-navy-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-navy-700 active:bg-navy-900 focus:outline-none focus:ring-2 focus:ring-navy-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
            Partager
        </a>
        <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="inline-flex items-center px-4 py-2 bg-gold-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gold-700 active:bg-gold-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Modifier
        </a>
    </div>
</div>

@if(session('share_url'))
<div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-700 flex items-center justify-between shadow-sm rounded-r-md" x-data="{ copied: false }">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
        <span class="text-sm font-medium">Lien de partage : <code class="bg-blue-100 px-2 py-1 rounded">{{ session('share_url') }}</code></span>
    </div>
    <button @click="navigator.clipboard.writeText('{{ session('share_url') }}'); copied = true; setTimeout(() => copied = false, 2000)" class="ml-4 flex-shrink-0 bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded text-xs font-bold transition">
        <span x-show="!copied">Copier</span>
        <span x-show="copied">Copié !</span>
    </button>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-navy-600 uppercase tracking-wider">Articles & Services</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qté</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Prix Unitaire</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->description }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 text-center">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 text-sm font-bold text-navy-600 text-right">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-500 uppercase">Total</td>
                        <td class="px-6 py-4 text-right text-xl font-bold text-navy-600">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($invoice->notes)
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-bold text-navy-600 uppercase tracking-wider mb-2">Notes</h2>
            <div class="text-sm text-gray-600 whitespace-pre-line">
                {{ $invoice->notes }}
            </div>
        </div>
        @endif
    </div>

    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-bold text-navy-600 uppercase tracking-wider mb-4 border-b pb-2">Client</h2>
            <div class="space-y-3">
                <p class="text-sm font-bold text-gray-900">{{ $invoice->client_name }}</p>
                @if($invoice->client_email)
                <p class="text-sm text-gray-600 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    {{ $invoice->client_email }}
                </p>
                @endif
                @if($invoice->client_phone)
                <p class="text-sm text-gray-600 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    {{ $invoice->client_phone }}
                </p>
                @endif
                @if($invoice->client_address)
                <div class="text-sm text-gray-600 flex items-start">
                    <svg class="w-4 h-4 mr-2 mt-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="whitespace-pre-line">{{ $invoice->client_address }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-bold text-navy-600 uppercase tracking-wider mb-4 border-b pb-2">Informations</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Statut:</span>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($invoice->status == 'paid') bg-green-100 text-green-800 
                        @elseif($invoice->status == 'sent') bg-blue-100 text-blue-800
                        @elseif($invoice->status == 'overdue') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif uppercase">
                        {{ $invoice->status }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Date d'échéance:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</span>
                </div>
                @if($invoice->quote)
                <div class="border-t pt-3 flex justify-between items-center">
                    <span class="text-sm text-gray-500">Devis lié:</span>
                    <a href="{{ route('admin.quotes.show', $invoice->quote->id) }}" class="text-sm font-bold text-gold-600 hover:text-gold-900">{{ $invoice->quote->number }}</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
