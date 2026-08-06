@extends('layouts.admin')
@section('title', 'Gestion des Accès')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Gestion des Accès & Profils</h1>
        <p class="text-sm text-gray-500 mt-0.5">Attribuez les autorisations d'accès aux différents modules pour chaque rôle de l'équipe.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md font-bold text-sm hover:bg-gray-50 transition shadow-sm gap-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Membres de l'équipe
        </a>
    </div>
</div>

<form action="{{ route('admin.users.permissions.update') }}" method="POST">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @foreach($roles as $roleKey => $roleLabel)
            @php
                $roleColors = [
                    'dg' => ['bg' => 'bg-purple-50/80', 'border' => 'border-purple-200', 'text' => 'text-purple-900', 'bullet' => 'bg-purple-500'],
                    'commercial' => ['bg' => 'bg-blue-50/80', 'border' => 'border-blue-200', 'text' => 'text-blue-900', 'bullet' => 'bg-blue-500'],
                    'comptable' => ['bg' => 'bg-amber-50/80', 'border' => 'border-amber-200', 'text' => 'text-amber-900', 'bullet' => 'bg-amber-500'],
                    'magasinier' => ['bg' => 'bg-green-50/80', 'border' => 'border-green-200', 'text' => 'text-green-900', 'bullet' => 'bg-green-500'],
                    'technicien' => ['bg' => 'bg-cyan-50/80', 'border' => 'border-cyan-200', 'text' => 'text-cyan-900', 'bullet' => 'bg-cyan-500'],
                    'livreur' => ['bg' => 'bg-orange-50/80', 'border' => 'border-orange-200', 'text' => 'text-orange-900', 'bullet' => 'bg-orange-500'],
                ];
                $color = $roleColors[$roleKey] ?? ['bg' => 'bg-gray-50/80', 'border' => 'border-gray-200', 'text' => 'text-gray-900', 'bullet' => 'bg-gray-500'];
                $currentPerms = $rolePermissions[$roleKey] ?? [];
            @endphp

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col transition hover:shadow-md" x-data="{ checkedCount: {{ count(array_intersect($currentPerms, array_keys($modules))) }} }">
                <!-- Header -->
                <div class="p-5 border-b border-gray-100 flex items-center justify-between {{ $color['bg'] }}">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full {{ $color['bullet'] }}"></span>
                        <h3 class="text-lg font-bold {{ $color['text'] }}">{{ $roleLabel }}</h3>
                    </div>
                    <button type="button" @click="
                        const checkboxes = $el.closest('.bg-white').querySelectorAll('input[type=checkbox]');
                        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                        checkboxes.forEach(cb => cb.checked = !allChecked);
                        checkedCount = allChecked ? 0 : checkboxes.length;
                    " class="text-xs font-bold px-2.5 py-1 rounded bg-white/90 hover:bg-white border border-gray-200 transition shadow-sm text-gray-700">
                        Tout Cocher / Décocher
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 flex-1">
                    <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-4">Modules autorisés :</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($modules as $modKey => $modLabel)
                            @php
                                $isChecked = in_array($modKey, $currentPerms);
                            @endphp
                            <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50/50 cursor-pointer transition">
                                <input type="checkbox" name="permissions[{{ $roleKey }}][]" value="{{ $modKey }}" 
                                       class="mt-1 h-4 w-4 text-navy-600 border-gray-300 rounded focus:ring-gold-500"
                                       @change="checkedCount = $el.closest('.bg-white').querySelectorAll('input[type=checkbox]:checked').length"
                                       {{ $isChecked ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-gray-700 leading-tight">{{ $modLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-between items-center text-xs">
                    <span class="text-gray-500 font-medium">Profil : <span class="font-semibold text-navy-600">{{ $roleKey }}</span></span>
                    <span class="font-bold text-gray-700" x-text="checkedCount + ' / {{ count($modules) }} modules'"></span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 flex justify-end">
        <button type="submit" class="inline-flex items-center px-6 py-3 bg-navy-600 text-white rounded-lg font-bold text-base hover:bg-navy-700 transition shadow-md gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Enregistrer les Accès
        </button>
    </div>
</form>

@endsection
