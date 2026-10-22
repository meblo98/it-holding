@extends('layouts.app')

@section('title', 'Déclarer une panne - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('layouts.client_sidebar')

            <main class="flex-1">
                <a href="{{ route('dashboard.care') }}" class="text-sm text-gray-500 hover:text-navy-900">← Mon Care+</a>
                <h1 class="mt-3 text-xl font-semibold text-navy-900">Déclarer une panne</h1>
                <p class="text-sm text-gray-500">{{ $subscription->product_name }} · abonnement {{ $subscription->number }} ({{ ucfirst($subscription->plan) }}). Aucune réparation payante ne démarre sans votre accord sur un devis.</p>

                <form method="POST" action="{{ route('dashboard.care.claim.store', $subscription->id) }}" enctype="multipart/form-data" class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-navy-900">Symptôme principal</label>
                        <input name="symptoms" value="{{ old('symptoms') }}" required maxlength="200" placeholder="Ex : ne s'allume plus, écran noir, surchauffe…" class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">
                        @error('symptoms')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-900">Description de la panne</label>
                        <textarea name="description" rows="5" required maxlength="2000" placeholder="Que s'est-il passé ? Que voyez-vous ? Qu'avez-vous déjà essayé ?" class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-900">Date d'apparition du problème</label>
                        <input type="date" name="started_at" value="{{ old('started_at') }}" max="{{ now()->toDateString() }}" class="mt-2 w-full sm:w-64 rounded-md border border-gray-200 px-3 py-2.5">
                        @error('started_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-900">Photos (facultatif, 4 maximum)</label>
                        <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm">
                        @error('photos')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        @error('photos.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-900">Vidéo (facultatif, 20 Mo maximum)</label>
                        <input type="file" name="video" accept="video/mp4,video/quicktime,video/webm" class="mt-2 block w-full text-sm">
                        @error('video')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <p class="text-sm font-medium text-navy-900">Comment nous remettre l'appareil ?</p>
                        <div class="mt-2 space-y-2 text-sm text-gray-700">
                            <label class="flex items-center gap-2"><input type="radio" name="drop_mode" value="depot" {{ old('drop_mode', 'depot') === 'depot' ? 'checked' : '' }}> Je dépose l'appareil à l'atelier</label>
                            <label class="flex items-center gap-2"><input type="radio" name="drop_mode" value="collecte" {{ old('drop_mode') === 'collecte' ? 'checked' : '' }}> Je demande une collecte (des frais de déplacement peuvent s'appliquer)</label>
                        </div>
                        @error('drop_mode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-900">Téléphone pour vous joindre</label>
                        <input name="contact_phone" value="{{ old('contact_phone', $subscription->client_phone) }}" required maxlength="30" class="mt-2 w-full sm:w-64 rounded-md border border-gray-200 px-3 py-2.5">
                        @error('contact_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="flex items-start gap-3 text-sm text-gray-600">
                            <input type="checkbox" name="declaration" value="1" class="mt-1" {{ old('declaration') ? 'checked' : '' }}>
                            <span>Je certifie que ces informations sont exactes. Je comprends que la prise en charge dépend du diagnostic, de ma formule et des exclusions.</span>
                        </label>
                        @error('declaration')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn-primary-gold w-full justify-center">Envoyer ma demande</button>
                </form>
            </main>
        </div>
    </div>
</div>
@endsection
