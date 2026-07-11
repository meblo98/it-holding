@extends('layouts.admin')
@section('title', 'Nouvel Utilisateur')
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">Créer un Utilisateur</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('admin.users.store') }}" method="POST" class="max-w-2xl">
    @csrf
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-5">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="admin-label">Nom complet <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-gold-500 focus:border-gold-500">
            </div>
            <div>
                <label class="admin-label">Téléphone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="admin-input">
            </div>
        </div>
        <div>
            <label class="admin-label">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-gold-500 focus:border-gold-500">
        </div>
        <div>
            <label class="admin-label">Rôle <span class="text-red-500">*</span></label>
            <select name="role" required class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-gold-500 focus:border-gold-500">
                <option value="">— Sélectionner un rôle —</option>
                @foreach($roles as $key => $label)
                    @if($key !== 'client')
                    <option value="{{ $key }}" {{ old('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="admin-label">Mot de passe <span class="text-red-500">*</span></label>
                <input type="password" name="password" required class="admin-input">
            </div>
            <div>
                <label class="admin-label">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" required class="admin-input">
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-navy-600 text-white font-bold px-6 py-2.5 rounded-md hover:bg-navy-700 transition text-sm">Créer l'utilisateur</button>
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2.5 text-sm">Annuler</a>
        </div>
    </div>
</form>
@endsection
