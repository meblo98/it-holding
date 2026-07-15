@extends('layouts.admin')

@section('title', 'Gestion Bancaire & Finance')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Gestion Financière & Trésorerie</h1>
        <p class="text-sm text-gray-500 mt-1">Gérez vos comptes bancaires, transactions et rapprochements financiers.</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="toggleModal('accountModal')" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-semibold text-sm hover:bg-navy-700 transition shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nouveau Compte Bancaire
        </button>
        <button onclick="toggleModal('transactionModal')" class="inline-flex items-center px-4 py-2 bg-gold-500 text-navy-900 rounded-md font-semibold text-sm hover:bg-gold-600 transition shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            Nouvelle Transaction
        </button>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded shadow-sm">
    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- BANK ACCOUNTS WIDGETS --}}
    @foreach($accounts as $acc)
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-navy-600 bg-navy-50 px-2.5 py-0.5 rounded-full">{{ $acc->bank_name }}</span>
                <span class="text-xs text-gray-400 font-mono">{{ $acc->rib ?? 'N/A' }}</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900">{{ $acc->name }}</h3>
            @if($acc->iban)
                <p class="text-xs text-gray-400 font-mono mt-1">IBAN: {{ $acc->iban }}</p>
            @endif
        </div>
        <div class="mt-6 border-t border-gray-50 pt-4 flex items-end justify-between">
            <span class="text-xs text-gray-500 font-semibold">Solde Actuel</span>
            <span class="text-2xl font-black text-navy-900">{{ number_format($acc->current_balance, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>
    @endforeach

    @if($accounts->isEmpty())
    <div class="lg:col-span-3 bg-gray-50 border-2 border-dashed border-gray-200 rounded-lg p-12 text-center">
        <p class="text-gray-500">Aucun compte bancaire configuré. Créez-en un pour commencer à suivre la trésorerie.</p>
    </div>
    @endif
</div>

{{-- TRANSACTIONS TABLE --}}
<div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100">
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-900">Journal des Transactions</h2>
    </div>

    @if($transactions->isEmpty())
    <div class="p-12 text-center">
        <p class="text-gray-500">Aucune transaction enregistrée.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compte</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence / Desc</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rapprochement</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($transactions as $tx)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $tx->transaction_date->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                        {{ $tx->bankAccount->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">{{ $tx->reference ?? '—' }}</div>
                        @if($tx->description)
                            <div class="text-xs text-gray-400">{{ $tx->description }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($tx->type === 'credit')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 border border-green-200 text-green-700">+ Entrée</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 border border-red-200 text-red-700">- Sortie</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-black {{ $tx->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($tx->amount, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($tx->is_reconciled)
                            <span class="inline-flex items-center text-xs font-bold text-green-700 bg-green-50 px-2 py-0.5 rounded">
                                ✅ Rapproché
                            </span>
                            @if($tx->invoice)
                                <div class="text-[10px] text-gray-400 mt-0.5">Facture #{{ $tx->invoice->number }}</div>
                            @elseif($tx->client)
                                <div class="text-[10px] text-gray-400 mt-0.5">Client: {{ $tx->client->display_name }}</div>
                            @endif
                        @else
                            <span class="inline-flex items-center text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">
                                ⏳ Non rapproché
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if(!$tx->is_reconciled)
                            <button onclick="openReconcileModal({{ $tx->id }}, '{{ $tx->type }}')" class="text-navy-600 hover:text-navy-900 bg-navy-50 hover:bg-navy-100 px-3 py-1 rounded transition text-xs font-bold">
                                Rapprocher
                            </button>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $transactions->links() }}
    </div>
    @endif
</div>

{{-- MODALS --}}
{{-- 1. Bank Account Modal --}}
<div id="accountModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="toggleModal('accountModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.finance.bank-accounts.store') }}" method="POST">
                @csrf
                <div class="bg-white px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Nouveau Compte Bancaire</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="admin-label">Nom du compte <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Ex: Compte Courant BNI" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="admin-label">Nom de la Banque <span class="text-red-500">*</span></label>
                        <input type="text" name="bank_name" required placeholder="Ex: BNI Sénégal" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="admin-label">RIB</label>
                            <input type="text" name="rib" placeholder="Code banque, guichet..." class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                        </div>
                        <div>
                            <label class="admin-label">Solde Initial (FCFA) <span class="text-red-500">*</span></label>
                            <input type="number" name="initial_balance" required min="0" value="0" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm font-bold text-right">
                        </div>
                    </div>
                    <div>
                        <label class="admin-label">IBAN</label>
                        <input type="text" name="iban" placeholder="SN76..." class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm font-mono">
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleModal('accountModal')" class="bg-white border border-gray-300 text-gray-700 font-semibold py-2 px-4 rounded text-sm hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="bg-navy-600 text-white font-bold py-2 px-4 rounded text-sm hover:bg-navy-700">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 2. Transaction Modal --}}
<div id="transactionModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="toggleModal('transactionModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.finance.transactions.store') }}" method="POST">
                @csrf
                <div class="bg-white px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Nouvelle Transaction</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="admin-label">Compte Bancaire <span class="text-red-500">*</span></label>
                            <select name="bank_account_id" required class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="admin-label">Type de Mouvement <span class="text-red-500">*</span></label>
                            <select name="type" required class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                                <option value="credit">Entrée (+ Crédit)</option>
                                <option value="debit">Sortie (- Débit)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="admin-label">Montant (FCFA) <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" required min="0.01" step="0.01" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm font-bold text-right">
                        </div>
                        <div>
                            <label class="admin-label">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="transaction_date" required value="{{ date('Y-m-d') }}" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="admin-label">Référence (N° chèque, virement...)</label>
                        <input type="text" name="reference" placeholder="Ex: VIR-CLIENT-1234" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="admin-label">Description</label>
                        <input type="text" name="description" placeholder="Ex: Paiement facture client..." class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                    </div>
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs font-bold text-navy-700 uppercase mb-2">Rapprochement Direct (Optionnel)</p>
                        <div>
                            <label class="admin-label">Lier à une facture impayée</label>
                            <select name="invoice_id" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                                <option value="">-- Aucune lier --</option>
                                @foreach($unreconciledInvoices as $inv)
                                    <option value="{{ $inv->id }}">Facture {{ $inv->number }} ({{ $inv->client_name }} - {{ number_format($inv->total_amount, 0) }} FCFA)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleModal('transactionModal')" class="bg-white border border-gray-300 text-gray-700 font-semibold py-2 px-4 rounded text-sm hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="bg-navy-600 text-white font-bold py-2 px-4 rounded text-sm hover:bg-navy-700">Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 3. Reconcile Modal --}}
<div id="reconcileModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="toggleModal('reconcileModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="reconcileForm" action="" method="POST">
                @csrf
                <div class="bg-white px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Rapprocher la Transaction</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div id="reconcileInvoiceBlock">
                        <label class="admin-label font-bold text-navy-800">Lier à une facture impayée</label>
                        <select name="invoice_id" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                            <option value="">-- Ne pas lier à une facture --</option>
                            @foreach($unreconciledInvoices as $inv)
                                <option value="{{ $inv->id }}">Facture {{ $inv->number }} ({{ $inv->client_name }} - {{ number_format($inv->total_amount, 0) }} FCFA)</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Marquera la facture comme payée et déduira le montant du solde du client.</p>
                    </div>
                    <div>
                        <label class="admin-label font-bold text-navy-800">Lier à un client (sans facture)</label>
                        <select name="client_id" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                            <option value="">-- Ne pas lier à un client --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleModal('reconcileModal')" class="bg-white border border-gray-300 text-gray-700 font-semibold py-2 px-4 rounded text-sm hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="bg-navy-600 text-white font-bold py-2 px-4 rounded text-sm hover:bg-navy-700">Rapprocher</button>
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

    function openReconcileModal(txId, txType) {
        const form = document.getElementById('reconcileForm');
        form.action = `/admin/finance/reconcile/${txId}`;
        
        const invoiceBlock = document.getElementById('reconcileInvoiceBlock');
        if (txType === 'debit') {
            invoiceBlock.style.display = 'none';
        } else {
            invoiceBlock.style.display = 'block';
        }

        toggleModal('reconcileModal');
    }
</script>
@endsection
