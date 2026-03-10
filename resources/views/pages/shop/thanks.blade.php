@extends('layouts.app')

@section('title', 'Merci pour votre commande - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <!-- Success Animation/Icon -->
        <div class="mb-10 inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full text-green-600 shadow-lg shadow-green-100 animate-bounce">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-10 lg:p-16 border border-gray-100">
            <h1 class="text-3xl font-black text-navy-900 uppercase tracking-tighter italic mb-4">Merci pour votre commande !</h1>
            <p class="text-gray-500 mb-10 leading-relaxed">
                Votre commande <span class="text-gold-600 font-bold">#{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</span> a bien été enregistrée. 
                Une confirmation a été envoyée à <span class="font-bold text-navy-800">{{ $order->customer_email }}</span>.
            </p>

            <!-- Order Brief Details -->
            <div class="bg-gray-50 rounded-xl p-8 mb-10 text-left">
                <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-6 border-b border-gray-100 pb-3 italic">Détails de l'Expédition</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Total payé:</span>
                        <span class="font-black text-navy-900 text-lg">{{ number_format($order->total_amount * 1.18, 0, ',', ' ') }} CFA</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Mode de paiement:</span>
                        <span class="font-bold text-gold-600 uppercase">{{ $order->payment_method }} (À la livraison)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Destinataire:</span>
                        <span class="font-bold text-navy-800">{{ $order->customer_name }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}" class="btn-primary-gold px-10 py-4 uppercase tracking-[0.2em] text-[10px] flex items-center justify-center gap-3">
                    RETOUR À L'ACCUEIL
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </a>
                <a href="{{ route('shop.index') }}" class="bg-navy-900 text-white px-10 py-4 rounded-md font-bold uppercase tracking-[0.2em] text-[10px] hover:bg-navy-700 transition-all flex items-center justify-center gap-3">
                    CONTINUER VOS ACHATS
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </a>
            </div>
        </div>
        
        <p class="mt-8 text-xs text-gray-400 italic">Besoin d'aide ? Contactez notre support au <span class="font-bold text-navy-700">+221 77 351 87 16</span></p>
    </div>
</div>
@endsection
