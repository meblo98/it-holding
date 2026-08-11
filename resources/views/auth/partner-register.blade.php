@extends('layouts.app')

@section('title', 'Rejoindre IT Holding Partner - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen py-20 px-4">
    <div class="max-w-2xl mx-auto grid grid-cols-1 md:grid-cols-5 bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
        
        <!-- Left Side: Promo Info (2 cols) -->
        <div class="md:col-span-2 bg-navy-900 p-8 text-white flex flex-col justify-between">
            <div>
                <span class="text-gold-500 text-xs font-bold uppercase tracking-widest">IT Holding Partner</span>
                <h2 class="text-2xl font-black italic uppercase tracking-wider mt-2 mb-6">Devenez Partenaire Commercial</h2>
                
                <ul class="space-y-4 text-xs text-gray-300">
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-gold-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Gagnez jusqu'à <strong>20% de commission</strong> sur nos services.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-gold-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Pas de stock, pas de logistique, pas de boutique physique nécessaire.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-gold-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Un lien personnalisé unique pour promouvoir nos produits et services.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-gold-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Espace d'administration dédié pour suivre vos ventes et vos gains.</span>
                    </li>
                </ul>
            </div>
            
            <div class="mt-8 pt-6 border-t border-navy-800 text-[10px] text-gray-400 uppercase font-bold tracking-wider">
                IT Holding &copy; {{ date('Y') }}
            </div>
        </div>
        
        <!-- Right Side: Registration Form (3 cols) -->
        <div class="md:col-span-3 p-8 lg:p-10">
            <h3 class="text-lg font-black uppercase tracking-wider italic text-navy-900 mb-6">Créer un compte partenaire</h3>
            
            <form action="{{ route('partner.register') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Name field -->
                <div>
                    <label for="name" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Nom Complet</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                        class="w-full border-gray-200 rounded-lg py-2.5 px-3.5 text-xs focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 @error('name') border-red-500 @enderror"
                        placeholder="Ex: Mamadou Diallo">
                    @error('name')
                        <p class="mt-1 text-[9px] text-red-500 font-bold uppercase italic">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email field -->
                <div>
                    <label for="email" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Adresse Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full border-gray-200 rounded-lg py-2.5 px-3.5 text-xs focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 @error('email') border-red-500 @enderror"
                        placeholder="Ex: mamadou@example.com">
                    @error('email')
                        <p class="mt-1 text-[9px] text-red-500 font-bold uppercase italic">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username field (used for the partner link) -->
                <div>
                    <label for="username" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Identifiant Unique (Lien Partenaire)</label>
                    <div class="relative flex items-stretch">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-200 bg-gray-50 text-[10px] text-gray-500 font-bold uppercase tracking-wider">
                            /partner/
                        </span>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required
                            class="w-full border-gray-200 rounded-r-lg py-2.5 px-3.5 text-xs focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 @error('username') border-red-500 @enderror"
                            placeholder="mamadou123">
                    </div>
                    <p class="mt-1 text-[9px] text-gray-400">Ce nom servira pour votre lien de recommandation unique.</p>
                    @error('username')
                        <p class="mt-1 text-[9px] text-red-500 font-bold uppercase italic">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone field -->
                <div>
                    <label for="phone" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Téléphone (WhatsApp)</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                        class="w-full border-gray-200 rounded-lg py-2.5 px-3.5 text-xs focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 @error('phone') border-red-500 @enderror"
                        placeholder="Ex: +221 77 000 00 00">
                    @error('phone')
                        <p class="mt-1 text-[9px] text-red-500 font-bold uppercase italic">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password field -->
                <div>
                    <label for="password" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Mot de passe</label>
                    <input type="password" name="password" id="password" required
                        class="w-full border-gray-200 rounded-lg py-2.5 px-3.5 text-xs focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 @error('password') border-red-500 @enderror"
                        placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-[9px] text-red-500 font-bold uppercase italic">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password confirmation field -->
                <div>
                    <label for="password_confirmation" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Confirmer le Mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full border-gray-200 rounded-lg py-2.5 px-3.5 text-xs focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center pt-2">
                    <input type="checkbox" id="terms" required class="rounded text-gold-500 focus:ring-gold-500 h-4 w-4 border-gray-200">
                    <label for="terms" class="ml-2 text-[9px] font-bold text-gray-400 uppercase tracking-tight">J'accepte le <a href="#" class="text-gold-600 underline">Contrat Partenaire IT Holding</a></label>
                </div>

                <button type="submit" class="w-full btn-primary-gold py-3 text-[10px] uppercase tracking-[0.2em] flex items-center justify-center gap-4 group mt-4">
                    SOUMETTRE MA CANDIDATURE
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>
            
            <p class="text-center text-[10px] text-gray-400 uppercase tracking-wider mt-6">
                Déjà partenaire ? <a href="{{ route('login') }}" class="text-gold-600 font-bold hover:underline">Se connecter</a>
            </p>
        </div>
    </div>
</div>
@endsection
