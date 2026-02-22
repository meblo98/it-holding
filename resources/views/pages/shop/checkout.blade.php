@extends('layouts.app')

@section('title', 'Paiement - ' . config('app.name'))

@section('content')
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto pt-12 pb-24 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 mb-2">Finaliser la commande</h1>
            <p class="text-gray-600 mb-8">Veuillez renseigner vos informations de livraison. Le paiement se fera à la
                livraison.</p>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <form action="{{ route('shop.placeOrder') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom complet</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adresse de livraison</label>
                            <textarea name="customer_address" rows="3" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">{{ old('customer_address') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mode de paiement</label>
                            <div class="mt-1">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="payment_method" value="cod" checked class="form-radio">
                                    <span class="ml-2">Paiement à la livraison (Cash on Delivery)</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                class="w-full inline-flex justify-center bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-xl shadow-lg py-3 px-6 text-lg font-semibold text-white hover:from-indigo-700 hover:to-indigo-800">
                                Confirmer la commande
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
