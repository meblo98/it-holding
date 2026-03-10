@extends('layouts.app')

@section('title', 'Contact - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen py-20 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Contact Card -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
            <!-- Header -->
            <div class="bg-navy-900 px-8 py-10 text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-gold-500/10 rounded-full -ml-12 -mb-12 blur-2xl"></div>
                
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter italic mb-4">Contactez-nous</h1>
                <p class="text-gray-400 text-sm italic font-medium max-w-md mx-auto">
                    Une question ? Un projet ? Notre équipe d'experts est à votre entière disposition.
                </p>
            </div>

            <div class="p-8 lg:p-12">
                @if (session('success'))
                    <div class="mb-8 bg-green-50 border border-green-100 text-green-700 px-6 py-4 rounded-xl flex items-center gap-4 animate-pulse">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm font-bold italic">{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Name field -->
                        <div class="space-y-2">
                            <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Nom complet</label>
                            <input type="text" name="name" id="name" required
                                class="w-full border-gray-200 rounded-lg py-3.5 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 transition-all placeholder-gray-300"
                                placeholder="Ex: Amadou Diop">
                        </div>

                        <!-- Email field -->
                        <div class="space-y-2">
                            <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Adresse Email</label>
                            <input type="email" name="email" id="email" required
                                class="w-full border-gray-200 rounded-lg py-3.5 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 transition-all placeholder-gray-300"
                                placeholder="contact@exemple.com">
                        </div>
                    </div>

                    <!-- Subject field -->
                    <div class="space-y-2">
                        <label for="subject" class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Sujet de votre message</label>
                        <input type="text" name="subject" id="subject" value="{{ $subject ?? '' }}"
                            class="w-full border-gray-200 rounded-lg py-3.5 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 transition-all placeholder-gray-300"
                            placeholder="En quoi pouvons-nous vous aider ?">
                    </div>

                    <!-- Message field -->
                    <div class="space-y-2">
                        <label for="message" class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Votre Message</label>
                        <textarea name="message" id="message" rows="5" required
                            class="w-full border-gray-200 rounded-lg py-3.5 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/30 transition-all placeholder-gray-300"
                            placeholder="Décrivez votre demande en détail..."></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full btn-primary-gold py-5 text-xs uppercase tracking-[0.2em] font-black flex items-center justify-center gap-4 group shadow-xl shadow-gold-500/10">
                            ENVOYER LE MESSAGE
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

                <div class="mt-12 pt-10 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-10 h-10 bg-navy-50 text-navy-900 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <p class="text-[9px] font-black text-navy-900 uppercase tracking-widest italic">+221 77 351 87 16</p>
                    </div>
                    <div class="text-center">
                        <div class="w-10 h-10 bg-gold-50 text-gold-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-[9px] font-black text-navy-900 uppercase tracking-widest italic truncate">Dakar, Sénégal</p>
                    </div>
                    <div class="text-center">
                        <div class="w-10 h-10 bg-navy-50 text-navy-900 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-[9px] font-black text-navy-900 uppercase tracking-widest italic truncate">contact@itholding.sn</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-center mt-10 text-[10px] text-gray-400 uppercase tracking-widest flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Réponse sous 24h ouvrées
        </p>
    </div>
</div>
@endsection
