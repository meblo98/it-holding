@extends('layouts.app')

@section('title', 'Inscription - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen py-20 px-4">
    <div class="max-w-md mx-auto">
        <!-- Tabbed Auth Card -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
            <!-- Tabs Header -->
            <div class="flex border-b border-gray-100">
                <a href="{{ route('login') }}" class="flex-1 py-5 text-center text-sm font-bold uppercase tracking-widest italic text-gray-300 hover:text-navy-900 transition-colors">
                    Se Connecter
                </a>
                <a href="{{ route('register') }}" class="flex-1 py-5 text-center text-sm font-black uppercase tracking-widest italic border-b-2 border-gold-500 text-navy-900">
                    S'inscrire
                </a>
            </div>

            <div class="p-8 lg:p-10">
                <form action="{{ route('register') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Name field -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Nom Complet</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                            class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-[10px] text-red-500 font-bold uppercase italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email field -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Adresse Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-[10px] text-red-500 font-bold uppercase italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password field -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Mot de passe</label>
                        <input type="password" name="password" id="password" required
                            class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-[10px] text-red-500 font-bold uppercase italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password confirmation field -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Confirmer le Mot de passe</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="terms" required class="rounded text-gold-500 focus:ring-gold-500 h-4 w-4 border-gray-200">
                        <label for="terms" class="ml-2 text-[10px] font-bold text-gray-400 uppercase tracking-tight">J'accepte les <a href="#" class="text-gold-600 underline">Conditions d'Utilisation</a></label>
                    </div>

                    <button type="submit" class="w-full btn-primary-gold py-4 text-xs uppercase tracking-[0.2em] flex items-center justify-center gap-4 group">
                        CRÉER UN COMPTE
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                    <div class="relative flex justify-center text-[10px] uppercase font-bold tracking-widest"><span class="px-3 bg-white text-gray-300">Ou</span></div>
                </div>

                <!-- Social Logins (Google only for register to keep it clean) -->
                <button class="w-full border border-gray-100 rounded-lg py-3 px-4 flex items-center justify-center gap-3 hover:bg-gray-50 transition-all">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-5 h-5">
                    <span class="text-xs font-bold text-navy-800 uppercase tracking-tight">S'inscrire avec Google</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
