@extends('layouts.app')

@section('title', 'Mon Panier - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto pt-12 pb-24 px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 mb-2">Mon Panier</h1>
        <p class="text-gray-600 mb-8">Gérez vos articles avant de passer commande</p>
        
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="lg:grid lg:grid-cols-12 lg:gap-x-8 lg:items-start">
            <section aria-labelledby="cart-heading" class="lg:col-span-7">
                <h2 id="cart-heading" class="sr-only">Articles dans votre panier</h2>

                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    @forelse($cart as $id => $details)
                        <div class="flex py-6 px-6 border-b border-gray-200 hover:bg-gray-50 transition-colors duration-200" data-product-id="{{ $id }}">
                            <div class="flex-shrink-0">
                                @if(isset($details['image']) && $details['image'])
                                    <img src="{{ asset('storage/' . $details['image']) }}" alt="{{ $details['name'] }}" class="w-24 h-24 rounded-lg object-center object-cover shadow-md">
                                @else
                                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center text-gray-400 shadow-md">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="ml-6 flex-1 flex flex-col">
                                <div class="flex justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <a href="{{ route('shop.show', $details['slug'] ?? '#') }}" class="hover:text-indigo-600 transition-colors duration-200">
                                                {{ $details['name'] }}
                                            </a>
                                        </h3>
                                        <p class="mt-2 text-xl font-bold text-indigo-600">{{ number_format($details['price'], 0, ',', ' ') }} FCFA</p>
                                    </div>

                                    <button type="button" onclick="removeFromCart({{ $id }})" class="ml-4 text-gray-400 hover:text-red-500 transition-colors duration-200">
                                        <span class="sr-only">Retirer</span>
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="mt-4 flex items-center space-x-4">
                                    <label for="quantity-{{ $id }}" class="text-sm font-medium text-gray-700">Quantité:</label>
                                    <div class="flex items-center space-x-3">
                                        <button type="button" onclick="updateQuantity({{ $id }}, -1)" class="inline-flex items-center justify-center w-8 h-8 border-2 border-gray-300 rounded-lg text-gray-600 hover:border-indigo-500 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        
                                        <input type="number" id="quantity-{{ $id }}" value="{{ $details['quantity'] }}" min="1" 
                                            onchange="updateQuantityDirect({{ $id }}, this.value)"
                                            class="w-16 text-center font-semibold border-2 border-gray-300 rounded-lg py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                                        
                                        <button type="button" onclick="updateQuantity({{ $id }}, 1)" class="inline-flex items-center justify-center w-8 h-8 border-2 border-gray-300 rounded-lg text-gray-600 hover:border-indigo-500 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 text-center">
                            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Votre panier est vide</h3>
                            <p class="mt-2 text-gray-500">Commencez vos achats dès maintenant !</p>
                            <div class="mt-6">
                                <a href="{{ route('shop.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                    Découvrir nos produits
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Order summary -->
            <section aria-labelledby="summary-heading" class="mt-8 bg-white rounded-2xl shadow-lg px-6 py-8 lg:mt-0 lg:col-span-5 sticky top-8">
                <h2 id="summary-heading" class="text-2xl font-bold text-gray-900 mb-6">Résumé</h2>

                <dl class="space-y-4">
                    <div class="flex items-center justify-between text-base text-gray-600">
                        <dt>Sous-total</dt>
                        <dd id="subtotal" class="font-medium">{{ number_format($total, 0, ',', ' ') }} FCFA</dd>
                    </div>
                    
                    <div class="flex items-center justify-between text-base text-gray-600">
                        <dt>Livraison</dt>
                        <dd class="font-medium">Calculée à la caisse</dd>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4 flex items-center justify-between">
                        <dt class="text-lg font-bold text-gray-900">Total</dt>
                        <dd id="total" class="text-2xl font-bold text-indigo-600">{{ number_format($total, 0, ',', ' ') }} FCFA</dd>
                    </div>
                </dl>

                @if(count($cart) > 0)
                    <div class="mt-8">
                        <button type="button" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-xl shadow-lg py-4 px-6 text-lg font-semibold text-white hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:scale-105 transition-all duration-200">
                            Passer à la caisse
                        </button>
                        <p class="mt-3 text-xs text-gray-500 text-center">Le paiement sera activé prochainement</p>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('shop.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200">
                            Continuer mes achats
                            <span aria-hidden="true"> →</span>
                        </a>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>

<script>
function updateQuantity(productId, change) {
    const input = document.getElementById('quantity-' + productId);
    const currentValue = parseInt(input.value);
    const newValue = currentValue + change;
    
    if (newValue >= 1) {
        updateQuantityDirect(productId, newValue);
    }
}

function updateQuantityDirect(productId, quantity) {
    quantity = parseInt(quantity);
    if (quantity < 1) quantity = 1;
    
    fetch('{{ route("shop.updateCart") }}', {
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
            document.getElementById('quantity-' + productId).value = quantity;
            document.getElementById('total').textContent = data.total;
            document.getElementById('subtotal').textContent = data.total;
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
        window.location.href = '{{ url("remove-from-cart") }}/' + productId;
    }
}
</script>
@endsection
