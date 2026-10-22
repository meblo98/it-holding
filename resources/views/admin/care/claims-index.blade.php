@extends('layouts.admin')
@section('title', 'Pannes Care+')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Pannes &amp; réparations Care+</h1>
        <p class="text-sm text-gray-500">Demandes déclarées par les clients.</p>
    </div>
    <a href="{{ route('admin.care.index') }}" class="text-sm font-bold text-navy-700 hover:underline">← Abonnements Care+</a>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <a href="{{ route('admin.care.claims.index', ['status' => 'open']) }}" class="bg-white rounded-lg shadow-sm border border-blue-100 p-4 block">
        <p class="text-xs font-bold text-blue-500 uppercase mb-1">Dossiers ouverts</p>
        <p class="text-2xl font-black text-blue-700">{{ $stats['open'] }}</p>
    </a>
    <a href="{{ route('admin.care.claims.index', ['status' => 'received']) }}" class="bg-white rounded-lg shadow-sm border border-amber-100 p-4 block">
        <p class="text-xs font-bold text-amber-500 uppercase mb-1">Nouvelles demandes</p>
        <p class="text-2xl font-black text-amber-700">{{ $stats['received'] }}</p>
    </a>
    <a href="{{ route('admin.care.claims.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 block">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Total</p>
        <p class="text-2xl font-black text-gray-900">{{ $stats['total'] }}</p>
    </a>
</div>

<form action="{{ route('admin.care.claims.index') }}" method="GET" class="mb-4 flex flex-wrap gap-2">
    <input name="search" value="{{ $search }}" placeholder="N° demande, client, appareil…" class="rounded-md border border-gray-300 px-3 py-2 text-sm w-64">
    <select name="status" class="rounded-md border border-gray-300 px-3 py-2 text-sm">
        <option value="">Tous les statuts</option>
        <option value="open" @selected($status === 'open')>Dossiers ouverts</option>
        @foreach(\App\Models\CareClaim::LABELS as $key => $label)
            <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <button class="px-4 py-2 bg-navy-900 text-white rounded-md text-sm font-bold">Filtrer</button>
</form>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-400">
            <tr>
                <th class="px-4 py-3">Demande</th>
                <th class="px-4 py-3">Client / appareil</th>
                <th class="px-4 py-3">Symptôme</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($claims as $claim)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><a href="{{ route('admin.care.claims.show', $claim->id) }}" class="font-bold text-navy-700 hover:underline">{{ $claim->number }}</a></td>
                    <td class="px-4 py-3">{{ $claim->subscription->client_name }}<br><span class="text-xs text-gray-400">{{ $claim->subscription->product_name }}</span></td>
                    <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($claim->symptoms, 40) }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ $claim->status_label }}</span></td>
                    <td class="px-4 py-3 text-gray-500">{{ $claim->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Aucune demande.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $claims->links() }}</div>
@endsection
