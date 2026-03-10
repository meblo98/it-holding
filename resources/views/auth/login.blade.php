@extends('layouts.app')

@section('title', 'Connexion - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen py-20 px-4">
    <div class="max-w-md mx-auto">
        <!-- Tabbed Auth Card -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
            <!-- Tabs Header -->
            <div class="flex border-b border-gray-100">
                <a href="{{ route('login') }}" class="flex-1 py-5 text-center text-sm font-black uppercase tracking-widest italic border-b-2 border-gold-500 text-navy-900">
                    Se Connecter
                </a>
                <a href="{{ route('register') }}" class="flex-1 py-5 text-center text-sm font-bold uppercase tracking-widest italic text-gray-300 hover:text-navy-900 transition-colors">
                    S'inscrire
                </a>
            </div>

            <div class="p-8 lg:p-10">
                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Email field -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Adresse Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-[10px] text-red-500 font-bold uppercase italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password field -->
                    <div>
                        <div class="flex justify-between mb-2">
                            <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Mot de passe</label>
                            <a href="#" class="text-[10px] font-bold text-gold-600 hover:underline uppercase tracking-tight">Mot de passe oublié ?</a>
                        </div>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 @error('password') border-red-500 @enderror">
                            <button type="button" class="absolute right-3 top-2.5 text-gray-300 hover:text-navy-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-[10px] text-red-500 font-bold uppercase italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="rounded text-gold-500 focus:ring-gold-500 h-4 w-4 border-gray-200">
                        <label for="remember" class="ml-2 text-xs font-bold text-gray-500 uppercase tracking-tight">Se souvenir de moi</label>
                    </div>

                    <button type="submit" class="w-full btn-primary-gold py-4 text-xs uppercase tracking-[0.2em] flex items-center justify-center gap-4 group">
                        SE CONNECTER
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                    <div class="relative flex justify-center text-[10px] uppercase font-bold tracking-widest"><span class="px-3 bg-white text-gray-300">Ou</span></div>
                </div>

                <!-- Social Logins -->
                <div class="space-y-4">
                    <button class="w-full border border-gray-100 rounded-lg py-3 px-4 flex items-center justify-center gap-3 hover:bg-gray-50 transition-all">
                        <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-5 h-5">
                        <span class="text-xs font-bold text-navy-800 uppercase tracking-tight">Se connecter avec Google</span>
                    </button>
                    <button class="w-full border border-gray-100 rounded-lg py-3 px-4 flex items-center justify-center gap-3 hover:bg-gray-50 transition-all">
                        <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0C4.477 0 0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987H5.898V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10c0-5.523-4.477-10-10-10z"/></svg>
                        <span class="text-xs font-bold text-navy-800 uppercase tracking-tight">Se connecter avec Apple</span>
                    </button>
                </div>
            </div>
        </div>
        
        <p class="text-center mt-10 text-[10px] text-gray-400 uppercase tracking-widest flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Paiement 100% Sécurisé & Données Protégées
        </p>
    </div>
</div>
@endsection
