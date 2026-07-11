@extends('layouts.admin')
@section('title', 'Clients CRM')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Clients</h1>
        <p class="text-sm text-gray-500 mt-0.5">Base clients professionnels et particuliers.</p>
    </div>
    <a href="{{ route('admin.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-bold text-sm hover:bg-navy-700 transition shadow-sm gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouveau Client
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif

{{-- KPI CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Total Clients</p>
        <p class="text-2xl font-black text-navy-900">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-4">
        <p class="text-xs font-bold text-blue-400 uppercase mb-1">Professionnels</p>
        <p class="text-2xl font-black text-blue-700">{{ $stats['professional'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Particuliers</p>
        <p class="text-2xl font-black text-gray-700">{{ $stats['individual'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-red-100 p-4">
        <p class="text-xs font-bold text-red-400 uppercase mb-1">Créances ouvertes</p>
        <p class="text-2xl font-black text-red-700">{{ $stats['with_credit'] }}</p>
    </div>
</div>

{{-- TYPE TABS --}}
<div class="flex gap-1 border-b border-gray-200 mb-5">
    <a href="{{ route('admin.clients.index') }}" class="px-5 py-2.5 text-sm font-semibold border-b-2 transition -mb-px {{ !$type ? 'border-navy-600 text-navy-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Tous</a>
    <a href="{{ route('admin.clients.index', ['type' => 'professional']) }}" class="px-5 py-2.5 text-sm font-semibold border-b-2 transition -mb-px {{ $type === 'professional' ? 'border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">🏢 Professionnels</a>
    <a href="{{ route('admin.clients.index', ['type' => 'individual']) }}" class="px-5 py-2.5 text-sm font-semibold border-b-2 transition -mb-px {{ $type === 'individual' ? 'border-green-500 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">👤 Particuliers</a>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <form action="{{ route('admin.clients.index') }}" method="GET" class="flex gap-2">
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Nom, email, entreprise, téléphone..."
                   class="block w-full max-w-sm border-gray-300 rounded-md shadow-sm text-sm focus:ring-gold-500 focus:border-gold-500">
            <button type="submit" class="bg-navy-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-navy-700 transition">Rechercher</button>
            @if($search)
            <a href="{{ route('admin.clients.index', ['type' => $type]) }}" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">Effacer</a>
            @endif
        </form>
    </div>

    @if($clients->isEmpty())
    <div class="p-12 text-center">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <p class="text-gray-500 font-medium">Aucun client trouvé</p>
        <a href="{{ route('admin.clients.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-bold text-sm hover:bg-navy-700 transition">Créer un client</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Commandes</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Solde dû</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($clients as $client)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.clients.show', $client->id) }}" class="text-sm font-bold text-navy-700 hover:underline">
                            {{ $client->full_name }}
                        </a>
                        @if($client->company_name)
                            <div class="text-xs text-blue-600 font-semibold">{{ $client->company_name }}</div>
                        @endif
                        @if($client->city)
                            <div class="text-xs text-gray-400">{{ $client->city }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div>{{ $client->email ?? '—' }}</div>
                        <div class="text-xs text-gray-400">{{ $client->phone ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($client->is_professional)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">Professionnel</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">Particulier</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">{{ $client->orders_count }}</td>
                    <td class="px-6 py-4 text-sm text-right font-bold {{ $client->current_balance > 0 ? 'text-red-600' : 'text-gray-400' }}">
                        {{ $client->current_balance > 0 ? number_format($client->current_balance, 0, ',', ' ') . ' FCFA' : '—' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.clients.show', $client->id) }}" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition">Fiche</a>
                            <a href="{{ route('admin.clients.edit', $client->id) }}" class="text-xs font-bold text-gold-600 bg-gold-50 hover:bg-gold-100 px-2.5 py-1 rounded transition">Modifier</a>
                            <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce client ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded transition">Suppr.</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $clients->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
