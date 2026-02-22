@extends('layouts.app')

@section('title', 'Merci pour votre commande - ' . config('app.name'))

@section('content')
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto pt-12 pb-24 px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-white rounded-2xl shadow-sm p-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Merci pour votre commande !</h1>
                <p class="text-gray-600 mb-6">Votre commande <strong>#{{ $order->id }}</strong> a bien été enregistrée.
                    Vous paierez à la livraison.</p>

                <div class="text-left mb-6">
                    <h3 class="font-semibold">Détails de la commande</h3>
                    <ul class="mt-3 space-y-2">
                        @foreach ($order->items as $item)
                            <li class="flex justify-between">
                                <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
                                <span>{{ number_format($item->price * $item->quantity, 0, ',', ' ') }} FCFA</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 flex justify-between font-bold">
                        <span>Total</span>
                        <span>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>

                <a href="{{ route('shop.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg">Retour aux produits</a>
            </div>
        </div>
    </div>
@endsection
