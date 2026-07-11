@extends('layouts.admin')
@section('title', 'Modifier ' . $user->name)
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">Modifier : {{ $user->name }}</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="max-w-2xl">
    @csrf @method('PUT')
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-5">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="admin-label">Nom complet</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="admin-input">
            </div>
            <div>
                <label class="admin-label">Téléphone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="admin-input">
            </div>
        </div>
        <div>
            <label class="admin-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Rôle</label>
            <select name="role" required class="admin-select">
                @foreach($roles as $key => $label)
                    @if($key !== 'client')
                    <option value="{{ $key }}" {{ $user->role === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="border-t border-gray-100 pt-4">
            <p class="text-xs text-gray-400 mb-3">Laisser vide pour ne pas changer le mot de passe.</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="admin-label">Nouveau mot de passe</label>
                    <input type="password" name="password" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Confirmer</label>
                    <input type="password" name="password_confirmation" class="admin-input">
                </div>
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-gold-600 text-white font-bold px-6 py-2.5 rounded-md hover:bg-gold-700 transition text-sm">Enregistrer</button>
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2.5 text-sm">Annuler</a>
        </div>
    </div>
</form>
@endsection
