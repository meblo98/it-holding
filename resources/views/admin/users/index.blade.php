@extends('layouts.admin')
@section('title', 'Utilisateurs Internes')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Équipe & Utilisateurs</h1>
        <p class="text-sm text-gray-500 mt-0.5">Gérez les comptes et les rôles du personnel interne.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-bold text-sm hover:bg-navy-700 transition shadow-sm gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvel Utilisateur
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-3 rounded text-sm text-red-800 font-medium">{{ session('error') }}</div>
@endif

{{-- ROLE TABS --}}
<div class="flex flex-wrap gap-2 mb-5">
    <a href="{{ route('admin.users.index') }}" class="px-4 py-1.5 rounded-full text-xs font-bold border transition {{ !$role ? 'bg-navy-600 text-white border-navy-600' : 'bg-white text-gray-600 border-gray-300 hover:border-navy-400' }}">Tous</a>
    @foreach(\App\Models\User::ROLES as $key => $label)
        @if($key !== 'client')
        <a href="{{ route('admin.users.index', ['role' => $key]) }}" class="px-4 py-1.5 rounded-full text-xs font-bold border transition {{ $role === $key ? 'bg-navy-600 text-white border-navy-600' : 'bg-white text-gray-600 border-gray-300 hover:border-navy-400' }}">{{ $label }}</a>
        @endif
    @endforeach
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-2">
            <input type="hidden" name="role" value="{{ $role }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par nom ou email..."
                   class="block w-full max-w-sm border-gray-300 rounded-md shadow-sm text-sm focus:ring-gold-500 focus:border-gold-500">
            <button type="submit" class="bg-navy-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-navy-700 transition">Rechercher</button>
        </form>
    </div>

    @if($users->isEmpty())
    <div class="p-12 text-center text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <p class="font-medium text-gray-500">Aucun utilisateur trouvé</p>
    </div>
    @else
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inscrit le</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($users as $user)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $user->photoUrl }}" alt="" class="w-9 h-9 rounded-full object-cover">
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    @php
                        $roleColors = ['admin'=>'red','dg'=>'purple','commercial'=>'blue','comptable'=>'amber','magasinier'=>'green','technicien'=>'cyan','livreur'=>'orange'];
                        $color = $roleColors[$user->role] ?? 'gray';
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-200">
                        {{ $user->role_label }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->phone ?? '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition">Modifier</a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded transition">Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $users->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
