@extends('layouts.app')

@section('title', 'Mon Panier - ' . config('app.name'))

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
                <span class="text-gold-600 font-medium truncate">Votre Panier</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
        @if (session('success'))
            <div class="mb-6 bg-green-50 text-green-700 p-4 rounded-lg flex items-center gap-3 border border-green-100 shadow-sm animate-fade-in">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
            <!-- Left Column: Shopping Card List -->
            <div class="lg:col-span-8">
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                        <h1 class="text-xl font-bold text-navy-900 uppercase tracking-tighter italic">Votre Panier d'Achat</h1>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 italic">
                                <tr>
                                    <th class="px-6 py-4">Produit</th>
                                    <th class="px-6 py-4 text-center">Prix</th>
                                    <th class="px-6 py-4 text-center">Quantité</th>
                                    <th class="px-6 py-4 text-right">Sous-Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($cart as $id => $details)
                                    <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                                        <td class="px-6 py-8">
                                            <div class="flex items-center gap-6">
                                                <button type="button" onclick="removeFromCart({{ $id }})" class="p-1 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-full transition-all">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                                <div class="flex items-center gap-4">
                                                    <div class="w-20 h-20 bg-gray-50 rounded-lg flex-shrink-0 flex items-center justify-center p-2 border border-gray-100">
                                                        @if (isset($details['image']) && $details['image'])
                                                            <img src="{{ asset('storage/' . $details['image']) }}" alt="{{ $details['name'] }}" class="max-h-full max-w-full object-contain">
                                                        @else
                                                            <img src="{{ asset('logo.jpeg') }}" class="max-h-full max-w-full object-contain opacity-20">
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('shop.show', $details['slug'] ?? '#') }}" class="text-sm font-bold text-navy-900 hover:text-gold-500 transition-colors line-clamp-2">{{ $details['name'] }}</a>
                                                        <span class="text-[10px] text-gray-400 uppercase tracking-widest block mt-1">Ref: #IT-{{ str_pad($id, 5, '0', STR_PAD_LEFT) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-8 text-center">
                                            <span class="text-sm font-bold text-navy-800">{{ number_format($details['price'], 0, ',', ' ') }} <span class="text-[10px]">CFA</span></span>
                                        </td>
                                        <td class="px-6 py-8 text-center">
                                            <div class="inline-flex items-center border border-gray-200 rounded-lg p-1 bg-white mx-auto">
                                                <button type="button" onclick="updateQuantity({{ $id }}, -1)" class="w-8 h-8 flex items-center justify-center text-navy-900 hover:bg-gray-50 rounded transition-colors">-</button>
                                                <input type="number" id="quantity-{{ $id }}" value="{{ $details['quantity'] }}" readonly class="w-10 text-center border-none bg-transparent font-bold focus:ring-0 text-sm text-navy-900">
                                                <button type="button" onclick="updateQuantity({{ $id }}, 1)" class="w-8 h-8 flex items-center justify-center text-navy-900 hover:bg-gray-50 rounded transition-colors">+</button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-8 text-right">
                                            <span class="text-sm font-black text-navy-900">{{ number_format($details['price'] * $details['quantity'], 0, ',', ' ') }} <span class="text-[10px]">CFA</span></span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                                </div>
                                                <h3 class="text-lg font-bold text-navy-900 italic uppercase">Votre panier est vide</h3>
                                                <p class="text-sm text-gray-400 mt-2 mb-8">Découvrez nos derniers produits informatiques premium.</p>
                                                <a href="{{ route('shop.index') }}" class="btn-primary-gold px-10 py-3 uppercase tracking-widest text-[10px]">Découvrir la Boutique</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(count($cart) > 0)
                    <div class="p-6 border-t border-gray-50 flex justify-between items-center bg-gray-50/10">
                        <a href="{{ route('shop.index') }}" class="text-navy-900 font-bold text-[10px] uppercase tracking-widest flex items-center gap-2 group">
                            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7"/></svg>
                            Retourner à la boutique
                        </a>
                        <button type="button" onclick="location.reload()" class="btn-primary-gold px-8 py-2.5 text-[10px] uppercase tracking-widest">Mettre à jour le panier</button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="lg:col-span-4 mt-12 lg:mt-0 sticky top-8 space-y-8">
                <!-- Card Totals -->
                <div class="bg-white border border-gray-100 rounded-xl shadow-md overflow-hidden p-8">
                    <h2 class="text-lg font-bold text-navy-900 uppercase tracking-tighter italic border-b border-gray-50 pb-4 mb-6">Total Panier</h2>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Sous-total</span>
                            <span class="font-bold text-navy-900">{{ number_format($total, 0, ',', ' ') }} CFA</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Livraison</span>
                            <span class="font-bold text-green-600">Gratuit</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Taxe / TVA (18%)</span>
                            <span class="font-bold text-navy-900">{{ number_format($total * 0.18, 0, ',', ' ') }} CFA</span>
                        </div>
                        <div class="border-t border-gray-50 pt-4 mt-4 flex justify-between items-end">
                            <span class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Total TTC</span>
                            <span class="text-2xl font-black text-gold-500">{{ number_format($total * 1.18, 0, ',', ' ') }} <span class="text-xs">CFA</span></span>
                        </div>
                    </div>

                    @if(count($cart) > 0)
                        <a href="{{ route('shop.checkout') }}" class="w-full btn-primary-gold py-4 text-xs uppercase tracking-[0.2em] flex items-center justify-center gap-4 group">
                            Paiement Sécurisé
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <div class="mt-8 flex items-center justify-center gap-4 grayscale opacity-40">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" class="h-4" title="Visa">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" class="h-6" title="Mastercard">
                        </div>
                    @endif
                </div>

                <!-- Coupon Section -->
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden p-8">
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4">Cidre de Promotion</h3>
                    <div class="relative">
                        <input type="text" placeholder="Entrez votre code" class="w-full border-gray-100 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/50">
                        <button type="button" class="mt-4 w-full bg-navy-900 text-white rounded-lg py-3 text-[10px] font-bold uppercase tracking-widest hover:bg-gold-500 hover:text-navy-900 transition-all">Appliquer le coupon</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateQuantity(productId, change) {
        const input = document.getElementById('quantity-' + productId);
        let currentValue = parseInt(input.value);
        let newValue = currentValue + change;

        if (newValue >= 1) {
            updateQuantityDirect(productId, newValue);
        }
    }

    function updateQuantityDirect(productId, quantity) {
        quantity = parseInt(quantity);
        if (quantity < 1) quantity = 1;

        fetch('{{ route('shop.updateCart') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Simpler for major UI overhaul to ensure all totals sync
                } else {
                    alert(data.message || 'Erreur lors de la mise à jour');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la mise à jour du panier');
            });
    }

    function removeFromCart(productId) {
        if (confirm('Voulez-vous vraiment retirer cet article du panier ?')) {
            window.location.href = '{{ url('remove-from-cart') }}/' + productId;
        }
    }
</script>
@endsection
