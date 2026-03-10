@extends('layouts.app')

@section('title', 'Paramètres du compte - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center italic">
                <a href="{{ route('home') }}" class="hover:text-navy-900 flex items-center gap-1">
                    <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Accueil
                </a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('dashboard') }}" class="hover:text-navy-900 transition-colors uppercase tracking-wider">Dashboard</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-navy-900 font-bold uppercase tracking-wider italic">Paramètres</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        @if(session('success'))
            <div class="mb-8 p-4 bg-green-50 border border-green-100 rounded-lg flex items-center gap-3 text-green-600 text-xs font-bold uppercase tracking-widest italic animate-bounce">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 p-4 bg-red-50 border border-red-100 rounded-lg text-red-600 text-xs font-bold uppercase tracking-widest italic space-y-1">
                @foreach($errors->all() as $error)
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $error }}
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            <aside class="w-full lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <nav class="flex flex-col">
                        @php
                            $navItems = [
                                ['name' => 'Tableau de bord', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>', 'route' => 'dashboard'],
                                ['name' => 'Historique des commandes', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'route' => 'dashboard.orders'],
                                ['name' => 'Suivi de commande', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>', 'route' => 'dashboard.track'],
                                ['name' => 'Panier', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>', 'route' => 'shop.cart'],
                                ['name' => 'Liste de souhaits', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>', 'route' => '#'],
                                ['name' => 'Cartes & Adresses', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>', 'route' => '#'],
                                ['name' => 'Paramètres', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>', 'route' => 'dashboard.settings'],
                            ];
                        @endphp
                        @foreach($navItems as $item)
                        <a href="{{ $item['route'] != '#' ? route($item['route']) : '#' }}" class="px-6 py-4 flex items-center gap-3 text-sm font-bold uppercase tracking-tight italic transition-all {{ request()->routeIs($item['route']) ? 'bg-gold-500 text-navy-900' : 'text-gray-400 hover:text-navy-900 hover:bg-gray-50 border-b border-gray-50' }}">
                            {!! $item['icon'] !!}
                            {{ $item['name'] }}
                        </a>
                        @endforeach
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-6 py-4 flex items-center gap-3 text-sm font-bold uppercase tracking-tight italic text-red-500 hover:bg-red-50 transition-all text-left">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Déconnexion
                            </button>
                        </form>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 space-y-8">
                <!-- Account Setting Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50">
                        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Informations de profil</h3>
                    </div>
                    <form action="{{ route('dashboard.settings.updateProfile') }}" method="POST" class="p-8">
                        @csrf
                        <div class="flex flex-col md:flex-row gap-8 items-start mb-8">
                            <div class="flex-shrink-0 relative group">
                                <img src="https://i.pravatar.cc/150?u={{ $user->id }}" class="w-24 h-24 rounded-full border-2 border-gold-500 shadow-sm transition-transform group-hover:scale-105">
                                <button type="button" class="absolute bottom-0 right-0 w-8 h-8 bg-white border border-gray-200 rounded-full flex items-center justify-center text-navy-900 shadow-sm hover:bg-gold-500 hover:border-gold-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </button>
                            </div>
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 italic">Nom d'affichage</label>
                                    <input type="text" name="display_name" value="{{ old('display_name', $user->name) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs font-bold text-navy-900 focus:ring-1 focus:ring-gold-500 focus:border-gold-500 outline-none transition-all italic">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 italic">Nom d'utilisateur</label>
                                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs font-bold text-navy-900 focus:ring-1 focus:ring-gold-500 focus:border-gold-500 outline-none transition-all italic">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 italic">Nom complet</label>
                                    <input type="text" name="full_name" value="{{ old('full_name', $user->name) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs font-bold text-navy-900 focus:ring-1 focus:ring-gold-500 focus:border-gold-500 outline-none transition-all italic">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 italic">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs font-bold text-navy-900 focus:ring-1 focus:ring-gold-500 focus:border-gold-500 outline-none transition-all italic">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 italic">Numéro de téléphone</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+221 ..." class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs font-bold text-navy-900 focus:ring-1 focus:ring-gold-500 focus:border-gold-500 outline-none transition-all italic">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 italic">Pays/Région</label>
                                    <select name="country" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs font-bold text-navy-900 focus:ring-1 focus:ring-gold-500 focus:border-gold-500 outline-none transition-all italic appearance-none">
                                        <option value="SN" {{ old('country', $user->country) == 'SN' ? 'selected' : '' }}>Sénégal</option>
                                        <option value="FR" {{ old('country', $user->country) == 'FR' ? 'selected' : '' }}>France</option>
                                        <option value="US" {{ old('country', $user->country) == 'US' ? 'selected' : '' }}>États-Unis</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="btn-primary-gold px-10 py-3 text-[10px] font-black uppercase tracking-widest italic shadow-lg shadow-gold-500/20 hover:scale-105 active:scale-95 transition-all">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Addresses Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 italic">
                    <!-- Billing Address -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center">
                            <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Adresse de facturation</h3>
                        </div>
                        <form action="{{ route('dashboard.settings.updateAddress') }}" method="POST" class="p-8 space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Prénom</label>
                                    <input type="text" name="billing_first_name" value="{{ old('billing_first_name', $user->billing_first_name) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded text-xs font-bold text-navy-900">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Nom</label>
                                    <input type="text" name="billing_last_name" value="{{ old('billing_last_name', $user->billing_last_name) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded text-xs font-bold text-navy-900">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Adresse</label>
                                <input type="text" name="billing_address" value="{{ old('billing_address', $user->billing_address) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded text-xs font-bold text-navy-900">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Ville</label>
                                    <input type="text" name="billing_city" value="{{ old('billing_city', $user->billing_city) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded text-xs font-bold text-navy-900">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Code Postal</label>
                                    <input type="text" name="billing_zip" value="{{ old('billing_zip', $user->billing_zip) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded text-xs font-bold text-navy-900">
                                </div>
                            </div>
                            <button type="submit" class="w-full btn-primary-gold px-6 py-3 text-[9px] font-black uppercase tracking-widest mt-4">Sauvegarder</button>
                        </form>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center">
                            <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Adresse de livraison</h3>
                        </div>
                        <form action="{{ route('dashboard.settings.updateAddress') }}" method="POST" class="p-8 space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Prénom</label>
                                    <input type="text" name="shipping_first_name" value="{{ old('shipping_first_name', $user->shipping_first_name) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded text-xs font-bold text-navy-900">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Nom</label>
                                    <input type="text" name="shipping_last_name" value="{{ old('shipping_last_name', $user->shipping_last_name) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded text-xs font-bold text-navy-900">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Adresse</label>
                                <input type="text" name="shipping_address" value="{{ old('shipping_address', $user->shipping_address) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded text-xs font-bold text-navy-900">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Ville</label>
                                    <input type="text" name="shipping_city" value="{{ old('shipping_city', $user->shipping_city) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded text-xs font-bold text-navy-900">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Code Postal</label>
                                    <input type="text" name="shipping_zip" value="{{ old('shipping_zip', $user->shipping_zip) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded text-xs font-bold text-navy-900">
                                </div>
                            </div>
                            <button type="submit" class="w-full btn-primary-gold px-6 py-3 text-[9px] font-black uppercase tracking-widest mt-4">Sauvegarder</button>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden italic">
                    <div class="px-8 py-6 border-b border-gray-50">
                        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Sécurité du compte</h3>
                    </div>
                    <form action="{{ route('dashboard.settings.updatePassword') }}" method="POST" class="p-8 space-y-6 max-w-2xl">
                        @csrf
                        <div class="space-y-1 relative">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 italic">Mot de passe actuel</label>
                            <input type="password" name="current_password" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs font-bold text-navy-900 focus:ring-1 focus:ring-gold-500 outline-none pr-12">
                            <button type="button" class="absolute right-4 bottom-3 text-gray-300 hover:text-navy-900" onclick="const p = this.previousElementSibling; p.type = p.type === 'password' ? 'text' : 'password'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        <div class="space-y-1 relative">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 italic">Nouveau mot de passe</label>
                            <input type="password" name="new_password" placeholder="8+ caractères" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs font-bold text-navy-900 focus:ring-1 focus:ring-gold-500 outline-none pr-12">
                            <button type="button" class="absolute right-4 bottom-3 text-gray-300 hover:text-navy-900" onclick="const p = this.previousElementSibling; p.type = p.type === 'password' ? 'text' : 'password'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        <div class="space-y-1 relative">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 italic">Confirmer le mot de passe</label>
                            <input type="password" name="new_password_confirmation" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs font-bold text-navy-900 focus:ring-1 focus:ring-gold-500 outline-none pr-12">
                            <button type="button" class="absolute right-4 bottom-3 text-gray-300 hover:text-navy-900" onclick="const p = this.previousElementSibling; p.type = p.type === 'password' ? 'text' : 'password'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        <button type="submit" class="btn-primary-gold px-10 py-3 text-[10px] font-black uppercase tracking-widest italic transition-all">
                            Changer le mot de passe
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
