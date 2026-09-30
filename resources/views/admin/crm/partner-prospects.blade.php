@extends('layouts.admin')
@section('title', 'Prospects du réseau')
@section('content')

<div class="mb-4">
    <h1 class="text-2xl font-bold text-gray-900">Prospects du réseau</h1>
    <p class="text-sm text-gray-500 mt-0.5">Tous les prospects et opportunités déclarés par les partenaires et apporteurs. Importez-les dans le pipeline pour les suivre avec l'équipe commerciale.</p>
</div>

@include('admin.crm._nav', ['active' => 'prospects'])

<form method="GET" class="flex flex-wrap gap-2 mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, société, téléphone…" class="admin-input max-w-sm">
    <select name="type" class="admin-select max-w-xs" onchange="this.form.submit()">
        <option value="">Prospects et opportunités</option>
        <option value="prospect" {{ request('type') === 'prospect' ? 'selected' : '' }}>Prospects des partenaires</option>
        <option value="opportunity" {{ request('type') === 'opportunity' ? 'selected' : '' }}>Opportunités des apporteurs</option>
    </select>
    <button type="submit" class="bg-navy-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-navy-700">Filtrer</button>
</form>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    @if($prospects->isEmpty())
        <div class="p-10 text-center text-gray-400 text-sm">Aucun prospect déclaré.</div>
    @else
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prospect</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Déclaré par</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Besoin</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pipeline</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($prospects as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="text-sm font-bold text-gray-900">{{ $p->name }}</div>
                    <div class="text-xs text-gray-500">{{ $p->company ?: '—' }} · {{ $p->phone ?: $p->email ?: '—' }}</div>
                    @if($p->entry_type === 'opportunity')
                        <span class="text-[10px] font-mono font-bold text-purple-700">{{ $p->opportunity_ref }}</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-sm text-gray-600">
                    {{ $p->partner?->name ?? '—' }}
                    <div class="text-[11px] text-gray-400">{{ $p->created_at->format('d/m/Y') }}</div>
                </td>
                <td class="px-5 py-3 text-xs text-gray-600 max-w-xs">{{ \Illuminate\Support\Str::limit($p->need, 90) ?: '—' }}</td>
                <td class="px-5 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $p->budget ? number_format($p->budget, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                <td class="px-5 py-3 text-right">
                    @if(isset($imported[$p->id]))
                        <a href="{{ route('admin.crm.show', $imported[$p->id]) }}" class="text-xs font-bold text-green-700 bg-green-50 hover:bg-green-100 px-2.5 py-1 rounded">Suivi ✓</a>
                    @else
                        <form action="{{ route('admin.crm.partner-prospects.import', $p->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded">Importer</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $prospects->links() }}</div>
    @endif
</div>
@endsection
