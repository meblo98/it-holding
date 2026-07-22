@extends('layouts.admin')
@section('title', $client->display_name)
@section('content')

{{-- Header --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.clients.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $client->full_name }}</h1>
            @if($client->company_name)
                <p class="text-sm text-blue-600 font-semibold">{{ $client->company_name }}</p>
            @endif
        </div>
        @if($client->is_professional)
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 border border-blue-200 text-blue-700">Professionnel</span>
        @endif
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.clients.edit', $client->id) }}" class="inline-flex items-center px-4 py-2 bg-gold-500 text-navy-900 font-bold rounded-md text-sm hover:bg-gold-600 transition shadow gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Modifier
        </a>
        <a href="{{ route('admin.delivery-notes.create', ['customer_name' => $client->full_name]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-bold rounded-md text-sm hover:bg-blue-700 transition shadow gap-2">
            Nouveau BL
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif

{{-- KPI CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 col-span-2">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">CA Total</p>
        <p class="text-xl font-black text-navy-900">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} <span class="text-sm font-semibold text-gray-400">FCFA</span></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Commandes</p>
        <p class="text-xl font-black text-gray-800">{{ $stats['total_orders'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Factures</p>
        <p class="text-xl font-black text-gray-800">{{ $stats['total_invoices'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-green-100 p-4">
        <p class="text-xs font-bold text-green-400 uppercase mb-1">Garanties actives</p>
        <p class="text-xl font-black text-green-700">{{ $stats['active_warranties'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-{{ $stats['balance'] > 0 ? 'red' : 'gray' }}-100 p-4">
        <p class="text-xs font-bold text-{{ $stats['balance'] > 0 ? 'red' : 'gray' }}-400 uppercase mb-1">Solde dû</p>
        <p class="text-xl font-black text-{{ $stats['balance'] > 0 ? 'red' : 'gray' }}-700">{{ number_format($stats['balance'], 0, ',', ' ') }} <span class="text-xs">FCFA</span></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-emerald-100 p-4">
        <p class="text-xs font-bold text-emerald-500 uppercase mb-1">Portefeuille Wallet</p>
        <p class="text-xl font-black text-emerald-700">{{ number_format($stats['wallet'], 0, ',', ' ') }} <span class="text-xs">FCFA</span></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- LEFT: HISTORY TABS --}}
    <div class="lg:col-span-2" x-data="{ tab: 'invoices' }">
        {{-- Tabs --}}
        <div class="flex border-b border-gray-200 mb-4 bg-white rounded-t-lg shadow-sm border border-b-0 border-gray-100 px-4">
            @foreach(['invoices' => 'Factures', 'orders' => 'Commandes', 'warranties' => 'Garanties', 'quotes' => 'Devis', 'wallet' => 'Portefeuille'] as $key => $label)
            <button @click="tab = '{{ $key }}'" class="px-4 py-3 text-sm font-semibold border-b-2 transition -mb-px" :class="tab === '{{ $key }}' ? 'border-navy-600 text-navy-700' : 'border-transparent text-gray-500 hover:text-gray-700'">{{ $label }}</button>
            @endforeach
        </div>

        {{-- Invoices --}}
        <div x-show="tab === 'invoices'" class="bg-white rounded-b-lg shadow-sm border border-gray-100 overflow-hidden">
            @if($client->invoices->isEmpty())
                <p class="p-6 text-sm text-gray-400 text-center">Aucune facture enregistrée.</p>
            @else
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Facture</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Statut</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($client->invoices->sortByDesc('created_at')->take(10) as $inv)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><a href="{{ route('admin.invoices.show', $inv->id) }}" class="font-bold text-navy-600 hover:underline">{{ $inv->number }}</a></td>
                    <td class="px-4 py-3 text-gray-500">{{ $inv->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900">{{ number_format($inv->total_amount ?? 0, 0, ',', ' ') }} FCFA</td>
                    <td class="px-4 py-3 text-right"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700">{{ $inv->status ?? 'Émise' }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        {{-- Orders --}}
        <div x-show="tab === 'orders'" x-cloak class="bg-white rounded-b-lg shadow-sm border border-gray-100 overflow-hidden">
            @if($client->orders->isEmpty())
                <p class="p-6 text-sm text-gray-400 text-center">Aucune commande.</p>
            @else
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commande</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Statut</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($client->orders->sortByDesc('created_at')->take(10) as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold text-navy-600 hover:underline">#{{ $order->id }}</a></td>
                    <td class="px-4 py-3 text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900">{{ number_format($order->total_amount ?? 0, 0, ',', ' ') }} FCFA</td>
                    <td class="px-4 py-3 text-right"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700">{{ $order->status }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        {{-- Warranties --}}
        <div x-show="tab === 'warranties'" x-cloak class="bg-white rounded-b-lg shadow-sm border border-gray-100 overflow-hidden">
            @if($client->warranties->isEmpty())
                <p class="p-6 text-sm text-gray-400 text-center">Aucune garantie.</p>
            @else
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Garantie</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiration</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Statut</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($client->warranties as $w)
                @php $cfg = \App\Models\Warranty::statusConfig($w->status); @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><a href="{{ route('admin.warranties.show', $w->id) }}" class="font-bold text-navy-600 hover:underline">{{ $w->number }}</a></td>
                    <td class="px-4 py-3 text-gray-700">{{ $w->product_name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $w->expiry_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right"><span class="px-2 py-0.5 rounded-full text-xs font-bold border {{ $cfg['classes'] }}">{{ $cfg['label'] }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        {{-- Quotes --}}
        <div x-show="tab === 'quotes'" x-cloak class="bg-white rounded-b-lg shadow-sm border border-gray-100 overflow-hidden">
            @if($client->quotes->isEmpty())
                <p class="p-6 text-sm text-gray-400 text-center">Aucun devis.</p>
            @else
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Devis</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($client->quotes->sortByDesc('created_at')->take(10) as $q)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><a href="{{ route('admin.quotes.show', $q->id) }}" class="font-bold text-navy-600 hover:underline">{{ $q->number }}</a></td>
                    <td class="px-4 py-3 text-gray-500">{{ $q->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900">{{ number_format($q->total_amount ?? 0, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        {{-- Wallet tab --}}
        <div x-show="tab === 'wallet'" x-cloak class="bg-white rounded-b-lg shadow-sm border border-gray-100 overflow-hidden">
            @if($client->walletTransactions->isEmpty())
                <p class="p-6 text-sm text-gray-400 text-center">Aucun mouvement sur le portefeuille.</p>
            @else
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($client->walletTransactions as $wt)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $wt->transaction_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        @if($wt->type === 'deposit')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-50 text-green-700">Dépôt</span>
                        @elseif($wt->type === 'payment')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-navy-50 text-navy-700">Achat</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-700">Remboursement</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $wt->description }}</td>
                    <td class="px-4 py-3 text-right font-black {{ $wt->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $wt->type === 'deposit' ? '+' : '-' }}{{ number_format($wt->amount, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
 
    {{-- RIGHT SIDEBAR --}}
    <div class="space-y-5">
        {{-- Contact --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Coordonnées</h3>
            <div class="space-y-2 text-sm">
                @if($client->email) <div class="flex items-center gap-2"><svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><span>{{ $client->email }}</span></div> @endif
                @if($client->phone) <div class="flex items-center gap-2"><svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg><span>{{ $client->phone }}</span></div> @endif
                @if($client->phone2) <div class="text-gray-500 text-xs pl-6">{{ $client->phone2 }}</div> @endif
                @if($client->address || $client->city) <div class="flex items-start gap-2 text-gray-600"><svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>{{ $client->address }}, {{ $client->city }}</span></div> @endif
            </div>
        </div>
 
        {{-- Actions Financières --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Actions Financières</h3>
            <div class="space-y-2">
                <button onclick="toggleModal('depositModal')" class="w-full text-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white text-sm font-bold rounded transition shadow-sm">
                    Dépôt Portefeuille
                </button>
                @if($client->current_balance > 0)
                <button onclick="toggleModal('payDebtModal')" class="w-full text-center px-4 py-2 bg-gradient-to-r from-navy-600 to-navy-700 hover:from-navy-700 hover:to-navy-800 text-white text-sm font-bold rounded transition shadow-sm">
                    Enregistrer un Règlement
                </button>
                @endif
            </div>
        </div>

        {{-- Pro info --}}
        @if($client->is_professional)
        <div class="bg-blue-50 rounded-lg border border-blue-100 p-5">
            <h3 class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-3">Infos Professionnelles</h3>
            <div class="space-y-2 text-sm">
                @if($client->rccm) <div><span class="text-xs text-gray-400 uppercase font-bold">RCCM : </span><span class="font-semibold text-gray-800">{{ $client->rccm }}</span></div> @endif
                @if($client->ninea) <div><span class="text-xs text-gray-400 uppercase font-bold">NINEA : </span><span class="font-semibold text-gray-800">{{ $client->ninea }}</span></div> @endif
                @if($client->sector) <div><span class="text-xs text-gray-400 uppercase font-bold">Secteur : </span><span>{{ $client->sector }}</span></div> @endif
                @if($client->credit_limit > 0) <div class="border-t border-blue-200 pt-2 mt-2"><span class="text-xs text-gray-400 uppercase font-bold">Plafond crédit : </span><span class="font-bold text-blue-700">{{ number_format($client->credit_limit, 0, ',', ' ') }} FCFA</span></div> @endif
                @if($client->payment_terms) <div><span class="text-xs text-gray-400 uppercase font-bold">Règlement : </span><span>{{ ['semaine'=>'Hebdomadaire','15j'=>'Bi-mensuel','mois'=>'Mensuel','trimestre'=>'Trimestriel'][$client->payment_terms] ?? $client->payment_terms }}</span></div> @endif
            </div>
        </div>
        @endif
 
        {{-- Notes --}}
        @if($client->notes)
        <div class="bg-amber-50 rounded-lg border border-amber-100 p-4">
            <h3 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-2">Notes internes</h3>
            <p class="text-sm text-gray-700">{{ $client->notes }}</p>
        </div>
        @endif
    </div>
</div>

{{-- MODALS FOR FINANCIAL ACTIONS --}}
{{-- 1. Deposit Modal --}}
<div id="depositModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="toggleModal('depositModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.clients.deposit', $client->id) }}" method="POST">
                @csrf
                <div class="bg-white px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Nouveau Dépôt Portefeuille</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" required min="1" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm font-bold text-right text-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description / Origine des fonds</label>
                        <input type="text" name="description" placeholder="Ex: Dépôt espèces en agence" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleModal('depositModal')" class="bg-white border border-gray-300 text-gray-700 font-semibold py-2 px-4 rounded text-sm hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="bg-green-600 text-white font-bold py-2 px-4 rounded text-sm hover:bg-green-700">Déposer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 2. Pay Debt Modal --}}
<div id="payDebtModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="toggleModal('payDebtModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.clients.pay-debt', $client->id) }}" method="POST">
                @csrf
                <div class="bg-white px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Enregistrer un Règlement</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="bg-red-50 border-l-4 border-red-400 p-3 rounded text-sm text-red-800">
                        Créance actuelle : <span class="font-bold">{{ number_format($client->current_balance, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Montant Réglé (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" required min="1" max="{{ $client->current_balance }}" value="{{ $client->current_balance }}" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm font-bold text-right text-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Compte Bancaire de Trésorerie</label>
                        <select name="bank_account_id" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                            <option value="">-- Ne pas lier à un compte bancaire --</option>
                            @foreach($bankAccounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} (Solde: {{ number_format($acc->current_balance, 0) }} FCFA)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description / Notes</label>
                        <input type="text" name="description" placeholder="Ex: Chèque n° 8765432" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleModal('payDebtModal')" class="bg-white border border-gray-300 text-gray-700 font-semibold py-2 px-4 rounded text-sm hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="bg-navy-600 text-white font-bold py-2 px-4 rounded text-sm hover:bg-navy-700">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection
