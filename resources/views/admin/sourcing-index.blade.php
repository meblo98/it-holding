@extends('layouts.admin')
@section('title', 'Appels d\'offres')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Appels d'offres fournisseurs</h1>
    <p class="text-sm text-gray-500">Chaque commande confirmée est proposée aux fournisseurs de la zone. La meilleure offre est retenue automatiquement.</p>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <a href="{{ route('admin.sourcing.index', ['status' => 'open']) }}" class="bg-white rounded-lg shadow-sm border border-blue-100 p-4 block">
        <p class="text-xs font-bold text-blue-500 uppercase mb-1">En cours</p>
        <p class="text-2xl font-black text-blue-700">{{ $stats['open'] }}</p>
    </a>
    <a href="{{ route('admin.sourcing.index', ['status' => 'failed']) }}" class="bg-white rounded-lg shadow-sm border border-red-100 p-4 block">
        <p class="text-xs font-bold text-red-500 uppercase mb-1">À traiter</p>
        <p class="text-2xl font-black text-red-700">{{ $stats['problem'] }}</p>
    </a>
    <a href="{{ route('admin.sourcing.index', ['status' => 'awarded']) }}" class="bg-white rounded-lg shadow-sm border border-green-100 p-4 block">
        <p class="text-xs font-bold text-green-500 uppercase mb-1">Offres retenues</p>
        <p class="text-2xl font-black text-green-700">{{ $stats['awarded'] }}</p>
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-400">
            <tr>
                <th class="px-4 py-3">Commande</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3">Réponses</th>
                <th class="px-4 py-3">Offre retenue</th>
                <th class="px-4 py-3">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($requests as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><a href="{{ route('admin.sourcing.show', $r->id) }}" class="font-bold text-navy-700 hover:underline">#{{ $r->order_id }}</a>
                        <span class="block text-xs text-gray-400">{{ $r->order->customer_name }}</span></td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ $r->status_label }}</span>
                        @if($r->status === 'open')<span class="block text-xs text-gray-400">jusqu'à {{ $r->deadline_at->format('H:i') }}</span>@endif</td>
                    <td class="px-4 py-3">{{ $r->received_count }} / {{ $r->offers_count }}</td>
                    <td class="px-4 py-3">
                        @if($r->awardedOffer)
                            {{ $r->awardedOffer->supplier->name }}<br>
                            <span class="text-xs text-gray-400">{{ number_format($r->awardedOffer->price, 0, ',', ' ') }} FCFA · {{ $r->awardedOffer->delay_hours }} h</span>
                        @else — @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Aucun appel d'offres.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $requests->links() }}</div>
@endsection
