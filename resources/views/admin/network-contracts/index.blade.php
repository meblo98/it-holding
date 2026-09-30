@extends('layouts.admin')
@section('title', 'Contrats Réseau Pro')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Contrats du réseau professionnel</h1>
        <p class="text-sm text-gray-500 mt-0.5">Un contrat en vigueur par catégorie. Une fois accepté par au moins un partenaire, il n'est plus modifiable — créez une nouvelle version.</p>
    </div>
    <a href="{{ route('admin.network-contracts.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-bold text-sm hover:bg-navy-700 transition shadow-sm gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouveau contrat
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-3 rounded text-sm text-red-800 font-medium">{{ session('error') }}</div>
@endif

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    @if($contracts->isEmpty())
    <div class="p-10 text-center text-gray-400 text-sm">
        Aucun contrat pour le moment. Tant qu'aucun contrat "en vigueur" n'existe pour une catégorie, les partenaires de cette catégorie ne sont pas bloqués.
    </div>
    @else
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Version</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acceptations</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($contracts as $contract)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $contract->title }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ \App\Models\User::PARTNER_TYPES[$contract->type] ?? ($contract->type === 'all' ? 'Toutes catégories' : $contract->type) }}</td>
                <td class="px-6 py-4 text-sm font-mono text-gray-600">v{{ $contract->version }}</td>
                <td class="px-6 py-4">
                    @php $badge = ['draft'=>'bg-yellow-50 text-yellow-700 border-yellow-200','active'=>'bg-green-50 text-green-700 border-green-200','archived'=>'bg-gray-100 text-gray-500 border-gray-200'][$contract->status] ?? ''; @endphp
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $badge }}">{{ \App\Models\Contract::STATUSES[$contract->status] ?? $contract->status }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $contract->acceptances_count }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('admin.network-contracts.show', $contract->id) }}" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition">Voir</a>
                        @if(!$contract->isLocked())
                            <a href="{{ route('admin.network-contracts.edit', $contract->id) }}" class="text-xs font-bold text-gold-600 bg-gold-50 hover:bg-gold-100 px-2.5 py-1 rounded transition">Modifier</a>
                            <form action="{{ route('admin.network-contracts.destroy', $contract->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce contrat ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded transition">Supprimer</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
