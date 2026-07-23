@extends('layouts.admin')

@section('title', 'Détails Commande #' . $order->id . ' - Admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Commande #{{ $order->id }}</h1>
                <a href="{{ route('admin.orders.index') }}" class="text-navy-600 hover:text-navy-900 font-medium">
                    &larr; Retour aux commandes
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Order Details -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Articles de la commande
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Liste des produits commandés.
                            </p>
                        </div>
                        <div class="border-t border-gray-200">
                            <ul role="list" class="divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                    <li class="px-4 py-4 sm:px-6 flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($item->product->image)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $item->product->image) }}" alt="">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gold-100 flex items-center justify-center text-gold-600 font-bold">
                                                    {{ substr($item->product->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <div class="text-sm font-medium text-navy-600 truncate">
                                                {{ $item->product->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                PU: {{ number_format($item->price, 0, ',', ' ') }} FCFA
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-900 font-medium">
                                            x {{ $item->quantity }}
                                        </div>
                                        <div class="ml-6 text-sm font-bold text-gray-900">
                                            {{ number_format($item->price * $item->quantity, 0, ',', ' ') }} FCFA
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @if(($order->promo_code_id && $order->discount_amount > 0) || ($order->tax_amount && $order->tax_amount > 0))
                        <div class="bg-gray-50 px-4 py-2 sm:px-6 flex justify-between items-center border-t border-gray-200 text-sm">
                            <div class="text-gray-500">Sous-total brut</div>
                            <div class="font-bold text-gray-700">{{ number_format($order->total_amount - $order->tax_amount + $order->discount_amount, 0, ',', ' ') }} FCFA</div>
                        </div>
                        @if($order->promo_code_id && $order->discount_amount > 0)
                        <div class="bg-gray-50 px-4 py-2 sm:px-6 flex justify-between items-center text-sm text-green-600">
                            <div>Remise Partenaire (Code: {{ $order->promoCode->code ?? 'N/A' }})</div>
                            <div class="font-bold">-{{ number_format($order->discount_amount, 0, ',', ' ') }} FCFA</div>
                        </div>
                        @endif
                        @if($order->tax_amount && $order->tax_amount > 0)
                        <div class="bg-gray-50 px-4 py-2 sm:px-6 flex justify-between items-center text-sm text-gray-500">
                            <div>Taxe / TVA (18%)</div>
                            <div class="font-bold text-gray-700">+{{ number_format($order->tax_amount, 0, ',', ' ') }} FCFA</div>
                        </div>
                        @endif
                        @endif
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between items-center border-t border-gray-200">
                            <div class="text-base font-medium text-gray-900 font-bold">Total payé/dû</div>
                            <div class="text-xl font-bold text-navy-900">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</div>
                        </div>
                    </div>

                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Informations Client
                            </h3>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                            <dl class="sm:divide-y sm:divide-gray-200">
                                @if($order->client)
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gold-50">
                                    <dt class="text-sm font-bold text-gold-800">Profil Client Associé</dt>
                                    <dd class="mt-1 text-sm text-gold-900 sm:mt-0 sm:col-span-2 font-bold">
                                        <a href="{{ route('admin.clients.show', $order->client_id) }}" class="underline hover:text-gold-700">
                                            {{ $order->client->display_name }} ({{ $order->client->company_name ?? 'Individuel' }})
                                        </a>
                                    </dd>
                                </div>
                                @endif
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->customer_name }}</dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->customer_email }}</dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->customer_phone ?? 'N/A' }}</dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Adresse de livraison</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->customer_address ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Order Processing -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Traitement
                            </h3>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Statut de la commande</label>
                                    <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gold-500 focus:border-gold-500 sm:text-sm rounded-md">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>En attente</option>
                                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>En cours</option>
                                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Terminée</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                                    </select>
                                </div>

                                <div class="mb-6">
                                    <label for="payment_status" class="block text-sm font-medium text-gray-700">Statut du paiement</label>
                                    <select id="payment_status" name="payment_status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gold-500 focus:border-gold-500 sm:text-sm rounded-md">
                                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>En attente</option>
                                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Payé</option>
                                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Échoué</option>
                                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Remboursé</option>
                                    </select>
                                </div>

                                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-navy-600 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500">
                                    Mettre à jour
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                    {{-- GENERATE DELIVERY NOTE --}}
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg border-l-4 border-blue-400">
                        <div class="px-4 py-4 sm:px-5 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                Bon de Livraison (Envoi)
                            </h3>
                        </div>
                        <div class="px-4 py-4 sm:p-5">
                            <p class="text-xs text-gray-500 mb-3">Créez un bon de livraison pré-rempli avec les articles de cette commande.</p>
                            <a href="{{ route('admin.delivery-notes.create', ['order_id' => $order->id]) }}" class="w-full inline-flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent shadow-sm text-sm font-bold rounded-md text-white bg-blue-600 hover:bg-blue-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                Générer un BL d'Envoi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
@endsection