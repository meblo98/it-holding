@extends('layouts.app')

@section('title', 'Finaliser la Commande - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-100 py-3 mb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center">
                <a href="{{ route('home') }}" class="hover:text-navy-900 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Accueil
                </a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('shop.cart') }}" class="hover:text-navy-900">Panier</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gold-600 font-medium truncate">Checkout</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
        <form action="{{ route('shop.placeOrder') }}" method="POST" class="lg:grid lg:grid-cols-12 lg:gap-x-12">
            @csrf
            
            <!-- Left Column: Billing & Payment -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Billing Information -->
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden p-8">
                    <h2 class="text-xl font-bold text-navy-900 uppercase tracking-tighter italic mb-8 border-b border-gray-50 pb-4">Coordonnées de Facturation</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Prénom</label>
                            <input type="text" name="first_name" value="{{ old('first_name', Auth::user()->billing_first_name ?? explode(' ', Auth::user()->name)[0] ?? '') }}" required class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30" placeholder="Votre prénom">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Nom</label>
                            <input type="text" name="last_name" value="{{ old('last_name', Auth::user()->billing_last_name ?? explode(' ', Auth::user()->name)[1] ?? '') }}" required class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30" placeholder="Votre nom">
                        </div>
                        
                        <div class="md:col-span-6">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Entreprise (Optionnel)</label>
                            <input type="text" name="company" class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30">
                        </div>
                        
                        <div class="md:col-span-6">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Adresse</label>
                            <input type="text" name="address" value="{{ old('address', Auth::user()->billing_address ?? '') }}" required class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30" placeholder="Adresse complète">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Pays</label>
                            <select name="country" class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30">
                                <option value="Sénégal" {{ old('country', Auth::user()->country ?? '') == 'SN' || old('country', Auth::user()->country ?? '') == 'Sénégal' ? 'selected' : '' }}>Sénégal</option>
                                <option value="France" {{ old('country', Auth::user()->country ?? '') == 'FR' || old('country', Auth::user()->country ?? '') == 'France' ? 'selected' : '' }}>France</option>
                                <option value="USA" {{ old('country', Auth::user()->country ?? '') == 'US' || old('country', Auth::user()->country ?? '') == 'USA' ? 'selected' : '' }}>USA</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Ville</label>
                            <input type="text" name="city" value="{{ old('city', Auth::user()->billing_city ?? '') }}" required class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Code Postal</label>
                            <input type="text" name="zip" value="{{ old('zip', Auth::user()->billing_zip ?? '') }}" required class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30">
                        </div>
                        
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email ?? '') }}" required class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30" placeholder="exemple@mail.com">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Numéro de Téléphone</label>
                            <input type="tel" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}" required class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30" placeholder="+221 ...">
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center">
                        <input type="checkbox" id="ship_different" class="rounded text-gold-500 focus:ring-gold-500 h-4 w-4 border-gray-200">
                        <label for="ship_different" class="ml-3 text-xs font-bold text-navy-800 uppercase tracking-tight">Expédier à une adresse différente</label>
                    </div>
                </div>

                <!-- Payment Options -->
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden p-8">
                    <h2 class="text-xl font-bold text-navy-900 uppercase tracking-tighter italic mb-8 border-b border-gray-50 pb-4">Mode de Paiement</h2>
                    
                    <div x-data="{ method: 'card' }" class="space-y-6">
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <!-- Payment Method Buttons -->
                            <template x-for="item in [
                                {id: 'cod', label: 'Espèces', icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'},
                                {id: 'wave', label: 'Wave/OM', icon: 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'},
                                {id: 'paypal', label: 'PayPal', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'},
                                {id: 'amazon', label: 'Amazon', icon: 'M20 12V8m0 0V5a2 2 0 012-2h-2m2 3v4m-2-3H4m0 0V5a2 2 0 00-2-2h2m-2 3v4m2-3h16'},
                                {id: 'card', label: 'Carte', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'}
                            ]">
                                <label :class="method === item.id ? 'border-gold-500 bg-gold-50/30' : 'border-gray-100 bg-white'" class="cursor-pointer border-2 rounded-xl p-4 flex flex-col items-center justify-center gap-3 transition-all">
                                    <input type="radio" name="payment_method" :value="item.id" x-model="method" class="sr-only">
                                    <svg class="w-6 h-6" :class="method === item.id ? 'text-gold-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"/></svg>
                                    <span class="text-[10px] font-bold uppercase tracking-widest" :class="method === item.id ? 'text-navy-900' : 'text-gray-400'" x-text="item.label"></span>
                                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center" :class="method === item.id ? 'border-gold-500' : 'border-gray-200'">
                                        <div x-show="method === item.id" class="w-2 h-2 rounded-full bg-gold-500"></div>
                                    </div>
                                </label>
                            </template>
                        </div>

                        <!-- Card Details (shown only if card is selected) -->
                        <div x-show="method === 'card'" x-transition class="space-y-6 pt-6 border-t border-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Nom sur la Carte</label>
                                    <input type="text" class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Numéro de Carte</label>
                                    <input type="text" placeholder="0000 0000 0000 0000" class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Date d'Expiration</label>
                                    <input type="text" placeholder="MM/YY" class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">CVC</label>
                                    <input type="text" placeholder="***" class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden p-8">
                    <h2 class="text-xl font-bold text-navy-900 uppercase tracking-tighter italic mb-8 border-b border-gray-50 pb-4">Informations Complémentaires</h2>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Notes de commande (Optionnel)</label>
                        <textarea name="notes" rows="4" class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30" placeholder="Notes concernant votre livraison..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="lg:col-span-4 mt-12 lg:mt-0 sticky top-8">
                <div class="bg-white border border-gray-100 rounded-xl shadow-md overflow-hidden p-8">
                    <h2 class="text-lg font-bold text-navy-900 uppercase tracking-tighter italic border-b border-gray-50 pb-4 mb-6">Résumé de la Commande</h2>
                    
                    <!-- Items List -->
                    <div class="space-y-4 mb-8">
                        @foreach($cart as $id => $item)
                        <div class="flex gap-4 items-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-lg p-2 border border-gray-50 flex-shrink-0 flex items-center justify-center">
                                @if(isset($item['image']) && $item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" class="max-h-full max-w-full object-contain">
                                @else
                                    <img src="{{ asset('logo.jpeg') }}" class="max-h-full max-w-full object-contain opacity-20">
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-navy-900 line-clamp-1 truncate w-40">{{ $item['name'] }}</p>
                                <p class="text-[10px] text-gray-400">{{ $item['quantity'] }} x <span class="text-gold-600 font-bold">{{ number_format($item['price'], 0, ',', ' ') }} CFA</span></p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Calculation -->
                    <div class="space-y-4 border-t border-gray-50 pt-6 mb-8 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Sous-total</span>
                            <span class="font-bold text-navy-900">{{ number_format($total, 0, ',', ' ') }} CFA</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Livraison</span>
                            <span class="font-bold text-green-600">Gratuit</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Remise</span>
                            <span class="font-bold text-red-500">0 CFA</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-xs">Taxe / TVA (18%)</span>
                            <span class="font-bold text-navy-900 text-xs">{{ number_format($total * 0.18, 0, ',', ' ') }} CFA</span>
                        </div>
                        <div class="border-t border-gray-50 pt-4 mt-4 flex justify-between items-end">
                            <span class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Total TTC</span>
                            <span class="text-2xl font-black text-gold-500">{{ number_format($total * 1.18, 0, ',', ' ') }} <span class="text-xs">CFA</span></span>
                        </div>
                    </div>

                    <button type="submit" class="w-full btn-primary-gold py-4 text-xs uppercase tracking-[0.2em] flex items-center justify-center gap-4 group">
                        CONFIRMER LA COMMANDE
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                    
                    <p class="mt-4 text-[9px] text-gray-400 text-center leading-relaxed italic">
                        En confirmant la commande, vous acceptez nos <a href="#" class="underline">Conditions Générales de Vente</a> et notre <a href="#" class="underline">Politique de Confidentialité</a>.
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
