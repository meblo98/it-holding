@extends('layouts.admin')
@section('title', 'Missions & Projets')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Missions & Projets</h1>
        <p class="text-sm text-gray-500 mt-0.5">Missions proposées aux freelances et prestataires du réseau. Une mission devient un projet dès qu'un professionnel est retenu.</p>
    </div>
    <a href="{{ route('admin.missions.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-bold text-sm hover:bg-navy-700 transition shadow-sm gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvelle mission
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif

<div class="flex gap-1 border-b border-gray-200 mb-5 overflow-x-auto">
    <a href="{{ route('admin.missions.index') }}" class="px-4 py-2.5 text-sm font-semibold border-b-2 whitespace-nowrap -mb-px {{ !request('status') ? 'border-navy-600 text-navy-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Toutes</a>
    @foreach(\App\Models\Mission::STATUSES as $key => $label)
        <a href="{{ route('admin.missions.index', ['status' => $key]) }}" class="px-4 py-2.5 text-sm font-semibold border-b-2 whitespace-nowrap -mb-px {{ request('status') === $key ? 'border-navy-600 text-navy-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    @if($missions->isEmpty())
        <div class="p-10 text-center text-gray-400 text-sm">Aucune mission.</div>
    @else
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mission</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lieu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Candidatures</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($missions as $mission)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900">{{ $mission->title }}</div>
                    <div class="text-xs font-mono text-gray-400">{{ $mission->reference }}@if($mission->project_ref) · Projet {{ $mission->project_ref }}@endif</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $mission->city ?: '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $mission->budget ? number_format($mission->budget, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $mission->applications_count }} <span class="text-gray-400">· {{ $mission->team_count }}/{{ $mission->positions }} retenu(s)</span></td>
                <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">{{ \App\Models\Mission::STATUSES[$mission->status] ?? $mission->status }}</span></td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('admin.missions.show', $mission->id) }}" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition">Gérer</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $missions->links() }}</div>
    @endif
</div>
@endsection
