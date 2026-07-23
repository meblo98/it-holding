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
                                    @php
                                        $productId = $details['product_id'] ?? $id;
                                        $product = \App\Models\Product::find($productId);
                                        $isWholesale = $product && $details['quantity'] >= ($product->wholesale_qty ?? 5) && ($product->wholesale_discount_rate > 0);

                                        $cartRawPath = $details['image'] ?: ($product ? ($product->images->first()?->path ?? null) : null);
                                        $cartImgUrl = $cartRawPath 
                                            ? (filter_var($cartRawPath, FILTER_VALIDATE_URL) 
                                                ? $cartRawPath 
                                                : (preg_match('#^/?storage/#', $cartRawPath) ? $cartRawPath : '/storage/' . ltrim($cartRawPath, '/'))) 
                                            : asset('logo.jpeg');
                                    @endphp
                                    <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                                        <td class="px-6 py-8">
                                            <div class="flex items-center gap-6">
                                                <button type="button" onclick="removeFromCart('{{ $id }}')" class="p-1 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-full transition-all">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                                <div class="flex items-center gap-4">
                                                    <div class="w-20 h-20 bg-gray-50 rounded-lg flex-shrink-0 flex items-center justify-center p-2 border border-gray-100">
                                                        <img src="{{ $cartImgUrl }}" alt="{{ $details['name'] }}" class="max-h-full max-w-full object-contain">
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('shop.show', $details['slug'] ?? '#') }}" class="text-sm font-bold text-navy-900 hover:text-gold-500 transition-colors line-clamp-2">{{ $details['name'] }}</a>
                                                        
                                                        @if(!empty($details['options']))
                                                            <div class="mt-1 text-xs text-gray-500 font-medium space-y-0.5">
                                                                @foreach($details['options'] as $opt)
                                                                    <span class="block text-navy-950">• {{ $opt['name'] }} : {{ $opt['value'] }} (+{{ number_format($opt['price'], 0, ',', ' ') }} F)</span>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        <span class="text-[10px] text-gray-400 uppercase tracking-widest block mt-1">Ref: #IT-{{ str_pad($productId, 5, '0', STR_PAD_LEFT) }}</span>
                                                        
                                                        @if($isWholesale)
                                                            <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[9px] font-black bg-gold-100 text-gold-700 uppercase tracking-widest animate-pulse border border-gold-200">
                                                                <svg class="w-3 h-3 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                                                </svg>
                                                                Tarif de gros (-{{ number_format($product->wholesale_discount_rate, 0) }}%)
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center gap-0.5">
                                                @if($isWholesale)
                                                    @php
                                                        $originalPrice = $product->promo_price && $product->promo_price > 0 && $product->promo_price < $product->price ? $product->promo_price : $product->price;
                                                    @endphp
                                                    <span class="text-xs text-gray-300 line-through font-medium">{{ number_format($originalPrice, 0, ',', ' ') }} CFA</span>
                                                @endif
                                                <span class="text-sm font-bold text-navy-800">{{ number_format($details['price'], 0, ',', ' ') }} <span class="text-[10px]">CFA</span></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-8 text-center">
                                            <div class="inline-flex items-center border border-gray-200 rounded-lg p-1 bg-white mx-auto">
                                                <button type="button" onclick="updateQuantity('{{ $id }}', -1)" class="w-8 h-8 flex items-center justify-center text-navy-900 hover:bg-gray-50 rounded transition-colors">-</button>
                                                <input type="number" id="quantity-{{ $id }}" value="{{ $details['quantity'] }}" readonly class="w-10 text-center border-none bg-transparent font-bold focus:ring-0 text-sm text-navy-900">
                                                <button type="button" onclick="updateQuantity('{{ $id }}', 1)" class="w-8 h-8 flex items-center justify-center text-navy-900 hover:bg-gray-50 rounded transition-colors">+</button>
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
                @php
                    $promoCode = null;
                    $discountAmount = 0;
                    if (Session::has('promo_code')) {
                        $promoCode = \App\Models\PartnerPromoCode::where('code', Session::get('promo_code'))->where('is_active', true)->first();
                        if ($promoCode) {
                            $discountAmount = ($total * $promoCode->discount_percent) / 100;
                        }
                    }
                    $discountedSubtotal = $total - $discountAmount;
                @endphp
                <div class="bg-white border border-gray-100 rounded-xl shadow-md overflow-hidden p-8">
                    <h2 class="text-lg font-bold text-navy-900 uppercase tracking-tighter italic border-b border-gray-50 pb-4 mb-6">Total Panier</h2>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Sous-total</span>
                            <span class="font-bold text-navy-900">{{ number_format($total, 0, ',', ' ') }} CFA</span>
                        </div>
                        @if($promoCode)
                        <div class="flex justify-between text-sm text-green-600 font-bold">
                            <span>Remise (Code: {{ $promoCode->code }} -{{ number_format($promoCode->discount_percent, 0) }}%)</span>
                            <span>-{{ number_format($discountAmount, 0, ',', ' ') }} CFA</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Livraison</span>
                            <span class="font-bold text-green-600">Gratuit</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Taxe / TVA (18%)</span>
                            <span class="font-bold text-navy-900">{{ number_format($discountedSubtotal * 0.18, 0, ',', ' ') }} CFA</span>
                        </div>
                        <div class="border-t border-gray-50 pt-4 mt-4 flex justify-between items-end">
                            <span class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Total TTC</span>
                            <span class="text-2xl font-black text-gold-500">{{ number_format($discountedSubtotal * 1.18, 0, ',', ' ') }} <span class="text-xs">CFA</span></span>
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
                    @if($promoCode)
                        <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4">Code Promo Appliqué</h3>
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-xs font-bold italic mb-4 flex items-center justify-between">
                            <span>Code : {{ $promoCode->code }}</span>
                            <span>-{{ number_format($promoCode->discount_percent, 0) }}%</span>
                        </div>
                        <form action="{{ route('shop.promo.remove') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 text-white rounded-lg py-3 text-[10px] font-bold uppercase tracking-widest hover:bg-red-700 transition-all">Retirer le code</button>
                        </form>
                    @else
                        <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4">Code de Promotion</h3>
                        <form action="{{ route('shop.promo.apply') }}" method="POST">
                            @csrf
                            <div class="relative">
                                <input type="text" name="code" required placeholder="Entrez votre code" class="w-full border-gray-100 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/50 uppercase tracking-widest">
                                <button type="submit" class="mt-4 w-full bg-navy-900 text-white rounded-lg py-3 text-[10px] font-bold uppercase tracking-widest hover:bg-gold-500 hover:text-navy-900 transition-all">Appliquer le code</button>
                            </div>
                        </form>
                    @endif
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
