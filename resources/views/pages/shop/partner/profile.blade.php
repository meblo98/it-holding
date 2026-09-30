@extends('layouts.app')

@section('title', 'Mon profil pro - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="bg-white border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center italic">
                <a href="{{ route('home') }}" class="hover:text-navy-900">Accueil</a>
                <span>›</span>
                <a href="{{ route('dashboard.partner') }}" class="hover:text-navy-900 uppercase tracking-wider">Espace Partenaire</a>
                <span>›</span>
                <span class="text-navy-900 font-bold uppercase tracking-wider">Mon profil pro</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('layouts.client_sidebar')

            <main class="flex-1 min-w-0 space-y-8">
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-xs font-bold italic">{{ session('success') }}</div>
                @endif

                @if($user->isPartner())
                    @include('pages.shop.partner._tabs', ['active' => 'profile'])
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Read-only: managed by IT Holding --}}
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                            <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Mon identité IT Holding</h3>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">ID professionnel</span>
                                @if($profile->pro_id)
                                    <a href="{{ route('professional.verify', $profile->pro_id) }}" target="_blank" class="text-sm font-mono font-bold text-gold-600 hover:underline">{{ $profile->pro_id }}</a>
                                @else
                                    <span class="text-xs text-gray-500">Attribué à la validation de votre compte.</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Catégorie</span>
                                <span class="text-sm font-bold text-navy-900">{{ $user->partner_type_label ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Niveau de vérification</span>
                                <span class="text-sm font-bold text-navy-900">{{ $profile->verification_level }}/5</span>
                                <span class="block text-xs text-gray-500">{{ $profile->verification_label }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Statut fiscal</span>
                                <span class="text-sm font-bold text-navy-900">{{ \App\Models\ProfessionalProfile::BENEFICIARY_TYPES[$profile->beneficiary_type ?? 'individual'] }}</span>
                                <span class="block text-[10px] text-gray-400">Déterminé par IT Holding, il conditionne la retenue à la source appliquée à vos paiements.</span>
                            </div>
                            @if($badges->isNotEmpty())
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Mes badges</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($badges as $ub)
                                        <a href="{{ route('badge.verify', $ub->number) }}" target="_blank" class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $ub->badge->color_classes }}">{{ $ub->badge->icon }} {{ $ub->badge->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Editable --}}
                    <form action="{{ route('dashboard.partner.profile.update') }}" method="POST" class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                        @csrf @method('PUT')
                        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Mes informations</h3>
                        <p class="text-xs text-gray-500">Ces informations permettent à IT Holding de vous proposer les missions et opportunités qui correspondent à votre profil.</p>

                        @if($errors->any())
                            <ul class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700 list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Ville</label>
                                <input type="text" name="city" value="{{ old('city', $profile->city) }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5" placeholder="Dakar, Thiès, Ziguinchor…">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Disponibilité</label>
                                <select name="availability" class="w-full text-sm border border-gray-200 rounded-lg p-2.5 bg-white">
                                    <option value="">—</option>
                                    @foreach(['available' => 'Disponible', 'busy' => 'Occupé', 'unavailable' => 'Indisponible'] as $key => $label)
                                        <option value="{{ $key }}" {{ old('availability', $profile->availability) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Compétences (séparées par des virgules)</label>
                            <input type="text" name="skills" value="{{ old('skills', implode(', ', $profile->skills ?? [])) }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5" placeholder="Vidéosurveillance, Réseau Cat6, Fibre optique">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Langues</label>
                            <input type="text" name="languages" value="{{ old('languages', implode(', ', $profile->languages ?? [])) }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5" placeholder="Français, Wolof, Anglais">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Présentation</label>
                            <textarea name="bio" rows="4" class="w-full text-sm border border-gray-200 rounded-lg p-2.5" placeholder="Votre expérience, vos réalisations, votre zone d'intervention…">{{ old('bio', $profile->bio) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">NINEA</label>
                            <input type="text" name="ninea" value="{{ old('ninea', $profile->ninea) }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5 max-w-xs">
                        </div>

                        @if($user->partner_type === 'prestataire')
                        <div class="border-t border-gray-100 pt-5 space-y-4">
                            <h4 class="text-[10px] font-black text-navy-900 uppercase tracking-widest">Ma structure <span class="text-gray-400 font-bold normal-case tracking-normal">— laissez vide si vous exercez en personne physique</span></h4>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Raison sociale</label>
                                <input type="text" name="company_name" value="{{ old('company_name', $profile->company_name) }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">RCCM</label>
                                    <input type="text" name="rccm" value="{{ old('rccm', $profile->rccm) }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Effectif</label>
                                    <input type="number" min="1" name="team_size" value="{{ old('team_size', $profile->team_size) }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Représentant légal</label>
                                    <input type="text" name="legal_representative" value="{{ old('legal_representative', $profile->legal_representative) }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Site web</label>
                                    <input type="url" name="website" value="{{ old('website', $profile->website) }}" class="w-full text-sm border border-gray-200 rounded-lg p-2.5" placeholder="https://">
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($profile->verification_level >= 3)
                            <p class="text-[10px] text-amber-700 bg-amber-50 border border-amber-200 rounded p-2">Modifier votre raison sociale, votre NINEA ou votre RCCM relancera la vérification de vos informations professionnelles par IT Holding.</p>
                        @endif

                        <button type="submit" class="btn-primary-gold px-8 py-3 text-[10px] uppercase tracking-widest">Enregistrer</button>
                    </form>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
