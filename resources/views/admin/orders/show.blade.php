@extends('layouts.admin')

@section('title', 'Détails Commande #' . $order->id . ' - Admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Commande #{{ $order->id }}</h1>
                <a href="{{ route('admin.orders.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
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
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold">
                                                    {{ substr($item->product->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <div class="text-sm font-medium text-indigo-600 truncate">
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
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between items-center border-t border-gray-200">
                            <div class="text-base font-medium text-gray-900">Total</div>
                            <div class="text-xl font-bold text-gray-900">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</div>
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
                                    <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>En attente</option>
                                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>En cours</option>
                                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Terminée</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                                    </select>
                                </div>

                                <div class="mb-6">
                                    <label for="payment_status" class="block text-sm font-medium text-gray-700">Statut du paiement</label>
                                    <select id="payment_status" name="payment_status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>En attente</option>
                                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Payé</option>
                                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Échoué</option>
                                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Remboursé</option>
                                    </select>
                                </div>

                                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Mettre à jour
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
@endsection
