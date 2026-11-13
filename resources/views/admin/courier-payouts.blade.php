@extends('layouts.admin')
@section('title', 'Revenus livreurs')
@section('content')
@php $fmt = fn ($n) => number_format($n, 0, ',', ' ') . ' FCFA'; @endphp

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Revenus des livreurs</h1>
    <p class="text-sm text-gray-500">Le revenu de chaque course est calculé à la livraison (forfait + prix au km, réglables dans config/delivery.php). Payez le livreur, puis marquez-le comme payé.</p>
</div>

<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-amber-100 p-4"><p class="text-xs font-bold text-amber-500 uppercase mb-1">À payer</p><p class="text-2xl font-black text-amber-700">{{ $fmt($totalUnpaid) }}</p></div>
    <div class="bg-white rounded-lg shadow-sm border border-green-100 p-4"><p class="text-xs font-bold text-green-500 uppercase mb-1">Payé ce mois</p><p class="text-2xl font-black text-green-700">{{ $fmt($paidThisMonth) }}</p></div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-400"><tr><th class="px-4 py-3">Livreur</th><th class="px-4 py-3">Courses</th><th class="px-4 py-3">À payer</th><th class="px-4 py-3"></th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rows as $r)
                @php $c = $couriers[$r->courier_id] ?? null; @endphp
                <tr>
                    <td class="px-4 py-3 font-bold">{{ $c?->name ?? 'Livreur #' . $r->courier_id }}<span class="block text-xs font-normal text-gray-400">{{ $c?->phone }}</span></td>
                    <td class="px-4 py-3">{{ $r->n }}</td>
                    <td class="px-4 py-3 font-black">{{ $fmt($r->total) }}</td>
                    <td class="px-4 py-3 text-right">
                        @if($c)
                            <form method="POST" action="{{ route('admin.courier-payouts.pay', $c->id) }}" onsubmit="return confirm('Confirmer le paiement de {{ $fmt($r->total) }} à {{ $c->name }} ?')">
                                @csrf<button class="px-4 py-2 bg-green-600 text-white font-bold rounded-md text-xs hover:bg-green-700">Marquer payé</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Aucun revenu en attente de paiement.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
