@extends('layouts.admin')
@section('title', 'Livraisons')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Livraisons</h1>
    <p class="text-sm text-gray-500">Chaque offre fournisseur retenue crée une livraison, attribuée automatiquement au livreur disponible le plus proche.</p>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <a href="{{ route('admin.deliveries.index', ['status' => 'pending']) }}" class="bg-white rounded-lg shadow-sm border border-red-100 p-4 block">
        <p class="text-xs font-bold text-red-500 uppercase mb-1">Sans livreur</p>
        <p class="text-2xl font-black text-red-700">{{ $stats['pending'] }}</p>
    </a>
    <a href="{{ route('admin.deliveries.index', ['status' => 'active']) }}" class="bg-white rounded-lg shadow-sm border border-blue-100 p-4 block">
        <p class="text-xs font-bold text-blue-500 uppercase mb-1">En cours</p>
        <p class="text-2xl font-black text-blue-700">{{ $stats['active'] }}</p>
    </a>
    <a href="{{ route('admin.deliveries.index', ['status' => 'delivered']) }}" class="bg-white rounded-lg shadow-sm border border-green-100 p-4 block">
        <p class="text-xs font-bold text-green-500 uppercase mb-1">Livrées</p>
        <p class="text-2xl font-black text-green-700">{{ $stats['delivered'] }}</p>
    </a>
</div>

<p class="mb-4 text-sm text-gray-500">Livreurs disponibles maintenant : <strong>{{ $couriers->count() }}</strong>
    @if($couriers->isNotEmpty()) ({{ $couriers->map(fn($c) => $c->user?->name)->filter()->implode(', ') }}) @endif</p>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-400">
            <tr><th class="px-4 py-3">Commande</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3">Livreur</th><th class="px-4 py-3">Retrait</th><th class="px-4 py-3">Date</th></tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($deliveries as $d)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><a href="{{ route('admin.deliveries.show', $d->id) }}" class="font-bold text-navy-700 hover:underline">#{{ $d->order_id }}</a><span class="block text-xs text-gray-400">{{ $d->order->customer_name }}</span></td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ $d->status_label }}</span></td>
                    <td class="px-4 py-3">{{ $d->courier?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ \Illuminate\Support\Str::limit($d->pickup_label, 40) }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Aucune livraison.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $deliveries->links() }}</div>
@endsection
