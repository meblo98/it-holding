@extends('layouts.app')

@section('title', 'Mon portefeuille - ' . config('app.name'))

@section('content')
@php $fcfa = fn ($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA'; @endphp
<div class="bg-gray-50 min-h-screen">
    <div class="bg-white border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center italic">
                <a href="{{ route('home') }}" class="hover:text-navy-900">Accueil</a>
                <span>›</span>
                <a href="{{ route('dashboard.partner') }}" class="hover:text-navy-900 uppercase tracking-wider">Espace Partenaire</a>
                <span>›</span>
                <span class="text-navy-900 font-bold uppercase tracking-wider">Portefeuille</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('layouts.client_sidebar')

            <main class="flex-1 min-w-0 space-y-8">
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-xs font-bold italic">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-xs font-bold italic">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                <ul class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700 list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                @endif

                @include('pages.shop.partner._tabs', ['active' => 'wallet'])

                {{-- Soldes (doc §38) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-1 bg-navy-900 text-white rounded-2xl p-6 shadow-lg border-b-4 border-gold-500">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gold-400">Solde disponible</span>
                        <div class="text-3xl font-black tracking-tight mt-2">{{ $fcfa($balances['available']) }}</div>
                        @if($balances['in_withdrawal'] > 0)
                            <div class="text-[11px] text-gray-300 mt-2">{{ $fcfa($balances['in_withdrawal']) }} en cours de retrait</div>
                        @endif
                    </div>
                    <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach([
                            ['En attente', $balances['pending'], 'Commandes non terminées, missions validées non payées (brut)', 'text-orange-600'],
                            ['Bloqué', $balances['blocked'], 'Gains retenus tant que votre contrat n\'est pas accepté (brut)', 'text-red-600'],
                            ['Total gagné', $balances['earned'], 'Net cumulé crédité sur ce portefeuille', 'text-navy-900'],
                            ['Total versé', $balances['paid_out'], 'Retraits versés et transferts en crédit boutique', 'text-navy-900'],
                            ['Retenues', $balances['withheld'], 'Retenues à la source prélevées (voir attestations)', 'text-gray-600'],
                        ] as [$label, $value, $hint, $color])
                            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4" title="{{ $hint }}">
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $label }}</span>
                                <div class="text-base font-black {{ $color }} mt-1 tracking-tight">{{ $fcfa($value) }}</div>
                                <div class="text-[10px] text-gray-400 mt-1 leading-tight">{{ $hint }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($balances['blocked'] > 0)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-xs text-red-700">
                        {{ $fcfa($balances['blocked']) }} sont bloqués : <a href="{{ route('dashboard.partner.contract') }}" class="font-bold underline">acceptez votre contrat</a> pour qu'ils soient versés automatiquement.
                    </div>
                @endif

                {{-- Actions --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <form action="{{ route('dashboard.partner.wallet.withdraw') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                        @csrf
                        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Demander un retrait</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Montant (FCFA)</label>
                                <input type="number" name="amount" min="1" max="{{ (int) floor($balances['available']) }}" step="1" required value="{{ old('amount') }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5" {{ $balances['available'] <= 0 ? 'disabled' : '' }}>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Moyen</label>
                                <select name="method" required class="w-full text-sm border border-gray-200 rounded-lg p-2.5 bg-white">
                                    @foreach(\App\Models\WithdrawalRequest::METHODS as $key => $label)
                                        <option value="{{ $key }}" {{ old('method') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Numéro ou compte de réception</label>
                            <input type="text" name="account_details" required value="{{ old('account_details', $user->phone) }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5" placeholder="77 000 00 00, ou RIB pour un virement">
                        </div>
                        <button type="submit" class="btn-primary-gold px-6 py-3 text-[10px] uppercase tracking-widest" {{ $balances['available'] <= 0 ? 'disabled' : '' }}>Demander le retrait</button>
                        <p class="text-[10px] text-gray-400">Le montant est réservé dès la demande et versé par IT Holding après vérification.</p>
                    </form>

                    <form action="{{ route('dashboard.partner.wallet.shop-credit') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                        @csrf
                        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Utiliser en boutique</h3>
                        <p class="text-xs text-gray-500">Transférez tout ou partie de votre solde en crédit boutique, utilisable immédiatement pour régler vos achats IT Holding.</p>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Montant (FCFA)</label>
                            <input type="number" name="amount" min="1" max="{{ (int) floor($balances['available']) }}" step="1" required class="w-full text-sm border border-gray-200 rounded-lg p-2.5" {{ $balances['available'] <= 0 ? 'disabled' : '' }}>
                        </div>
                        <button type="submit" class="bg-navy-900 hover:bg-navy-800 text-white px-6 py-3 rounded-lg text-[10px] font-black uppercase tracking-widest" {{ $balances['available'] <= 0 ? 'disabled' : '' }}>Transférer en crédit boutique</button>
                    </form>
                </div>

                {{-- Withdrawals --}}
                @if($withdrawals->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Mes demandes de retrait</h3>
                    </div>
                    <ul class="divide-y divide-gray-50">
                        @foreach($withdrawals as $w)
                        <li class="px-6 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                            <div>
                                <span class="font-mono font-bold text-navy-900">{{ $w->reference }}</span>
                                <span class="text-gray-500">· {{ $w->created_at->format('d/m/Y') }} · {{ $w->method_label }} ({{ $w->account_details }})</span>
                                @if($w->status === 'paid' && $w->payment_reference)<div class="text-gray-400">Réf. versement : {{ $w->payment_reference }}</div>@endif
                                @if($w->status === 'rejected' && $w->admin_note)<div class="text-red-600">Motif : {{ $w->admin_note }}</div>@endif
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-black text-navy-900">{{ $fcfa($w->amount) }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $w->status_classes }}">{{ $w->status_label }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Ledger --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Historique</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50 text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                <tr>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Opération</th>
                                    <th class="px-6 py-3 text-right">Brut</th>
                                    <th class="px-6 py-3 text-right">Retenue</th>
                                    <th class="px-6 py-3 text-right">Net</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-[11px] text-navy-700">
                                @forelse($entries as $entry)
                                <tr>
                                    <td class="px-6 py-3 whitespace-nowrap text-gray-500">{{ $entry->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-3">
                                        <span class="font-bold text-navy-900">{{ $entry->source_label }}</span>
                                        <div class="text-gray-500">{{ $entry->description }}</div>
                                    </td>
                                    <td class="px-6 py-3 text-right whitespace-nowrap text-gray-500">{{ $entry->gross_amount > 0 ? $fcfa($entry->gross_amount) : '' }}</td>
                                    <td class="px-6 py-3 text-right whitespace-nowrap">
                                        @if($entry->withholding_amount > 0)
                                            <span class="text-red-600">−{{ $fcfa($entry->withholding_amount) }}</span>
                                            @if(isset(\App\Models\ProWalletEntry::CERTIFIABLE_SOURCES[$entry->source_type]))
                                                <a href="{{ route('dashboard.partner.wallet.certificate', [\App\Models\ProWalletEntry::CERTIFIABLE_SOURCES[$entry->source_type], $entry->source_id]) }}" target="_blank" class="block text-[10px] font-bold text-gold-600 hover:underline">Attestation</a>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-right whitespace-nowrap font-black {{ $entry->isCredit() ? 'text-green-600' : 'text-navy-900' }}">{{ $entry->isCredit() ? '+' : '−' }}{{ $fcfa($entry->amount) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Aucune opération pour le moment.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($entries->hasPages())
                        <div class="px-6 py-3 border-t border-gray-50">{{ $entries->links() }}</div>
                    @endif
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
