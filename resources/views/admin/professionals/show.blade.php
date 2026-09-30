@extends('layouts.admin')
@section('title', 'Profil pro — ' . $user->name)
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $user->partner_type_label ?? 'Catégorie non définie' }}
                · {{ ['approved' => 'Actif', 'pending' => 'En attente', 'rejected' => 'Rejeté'][$user->partner_status] ?? $user->partner_status }}
                @if($profile?->pro_id) · <span class="font-mono">{{ $profile->pro_id }}</span>@endif
            </p>
        </div>
    </div>
    @if($profile?->pro_id)
    <div class="flex gap-2">
        <a href="{{ route('professional.verify', $profile->pro_id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-navy-700 rounded-md font-bold text-sm hover:bg-gray-50 transition shadow-sm">Page publique</a>
        <a href="{{ route('professional.qrcode.download', $profile->pro_id) }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-bold text-sm hover:bg-navy-700 transition shadow-sm">QR code du profil</a>
    </div>
    @endif
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-3 rounded text-sm text-red-800 font-medium">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Badges --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide">Badges</h2>
            </div>
            @if($userBadges->isEmpty())
                <div class="p-8 text-center text-sm text-gray-400">Aucun badge délivré.</div>
            @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Badge</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Validité</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($userBadges as $ub)
                    @php $state = $ub->display_status; @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $ub->badge->color_classes }}">{{ $ub->badge->icon }} {{ $ub->badge->name }}</span>
                            <div class="text-[11px] font-mono text-gray-400 mt-1">{{ $ub->number }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            {{ $ub->issued_at->format('d/m/Y') }} → {{ $ub->expires_at?->format('d/m/Y') ?? '∞' }}
                            @if($ub->issuer)<div class="text-gray-400">par {{ $ub->issuer->name }}</div>@endif
                        </td>
                        <td class="px-4 py-3">
                            @if($state === 'valid')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">Valide</span>
                            @elseif($state === 'expired')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">Expiré</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">Révoqué</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode(route('badge.verify', $ub->number)) }}" target="_blank" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition">QR</a>
                                @if($ub->status === 'active')
                                <form action="{{ route('admin.professionals.badges.revoke', $ub->id) }}" method="POST" class="inline" onsubmit="return confirm('Révoquer ce badge ? La page de vérification publique l\'affichera comme révoqué.')">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded transition">Révoquer</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6"
             x-data="{ validity: {{ Js::from($badges->pluck('validity_months', 'id')) }}, badgeId: '', issued: '{{ now()->toDateString() }}', expires: '',
                       sync() { const m = this.validity[this.badgeId]; if (!m) { this.expires = ''; return; } const d = new Date(this.issued); d.setMonth(d.getMonth() + m); this.expires = d.toISOString().slice(0, 10); } }">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide mb-4">Délivrer un badge</h2>
            <form action="{{ route('admin.professionals.badges.assign', $user->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div class="md:col-span-2">
                    <label class="admin-label">Badge</label>
                    <select name="badge_id" required class="admin-select" x-model="badgeId" @change="sync()">
                        <option value="">— Choisir —</option>
                        @foreach($badges as $b)
                            <option value="{{ $b->id }}">{{ $b->icon }} {{ $b->name }} — {{ $b->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label">Délivré le</label>
                    <input type="date" name="issued_at" required class="admin-input" x-model="issued" @change="sync()">
                </div>
                <div>
                    <label class="admin-label">Expire le (vide = sans expiration)</label>
                    <input type="date" name="expires_at" class="admin-input" x-model="expires">
                </div>
                <div class="md:col-span-2">
                    <label class="admin-label">Note interne (optionnel)</label>
                    <input type="text" name="notes" class="admin-input" placeholder="Ex: pièce d'identité contrôlée en agence">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="bg-gold-600 text-white font-bold px-6 py-2.5 rounded-md hover:bg-gold-700 transition text-sm">Délivrer le badge</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Profile & verification --}}
    <div>
        <form action="{{ route('admin.professionals.update', $user->id) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf @method('PUT')
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide">Vérification & profil</h2>

            <div>
                <label class="admin-label">Niveau de vérification</label>
                <select name="verification_level" class="admin-select">
                    @foreach(\App\Models\ProfessionalProfile::VERIFICATION_LEVELS as $lvl => $label)
                        <option value="{{ $lvl }}" {{ (int) old('verification_level', $profile->verification_level ?? 1) === $lvl ? 'selected' : '' }}>{{ $lvl }} — {{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="admin-label">Statut fiscal</label>
                <select name="beneficiary_type" class="admin-select">
                    @foreach(\App\Models\ProfessionalProfile::BENEFICIARY_TYPES as $key => $label)
                        <option value="{{ $key }}" {{ old('beneficiary_type', $profile->beneficiary_type ?? 'individual') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="admin-hint">Utilisé par le moteur fiscal pour déterminer la retenue applicable.</p>
            </div>

            <div>
                <label class="admin-label">NINEA</label>
                <input type="text" name="ninea" value="{{ old('ninea', $profile->ninea ?? '') }}" class="admin-input">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="admin-label">Ville</label>
                    <input type="text" name="city" value="{{ old('city', $profile->city ?? '') }}" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Disponibilité</label>
                    <select name="availability" class="admin-select">
                        <option value="">—</option>
                        @foreach(['available' => 'Disponible', 'busy' => 'Occupé', 'unavailable' => 'Indisponible'] as $key => $label)
                            <option value="{{ $key }}" {{ old('availability', $profile->availability ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="admin-label">Compétences (séparées par des virgules)</label>
                <input type="text" name="skills" value="{{ old('skills', implode(', ', $profile->skills ?? [])) }}" class="admin-input" placeholder="Réseau Cat6, Vidéosurveillance, Laravel">
            </div>

            <div>
                <label class="admin-label">Langues</label>
                <input type="text" name="languages" value="{{ old('languages', implode(', ', $profile->languages ?? [])) }}" class="admin-input" placeholder="Français, Wolof, Anglais">
            </div>

            <div>
                <label class="admin-label">Présentation</label>
                <textarea name="bio" rows="3" class="admin-textarea">{{ old('bio', $profile->bio ?? '') }}</textarea>
            </div>

            <button type="submit" class="w-full bg-navy-600 text-white font-bold px-5 py-2.5 rounded-md hover:bg-navy-700 transition text-sm">Enregistrer</button>
        </form>
    </div>
</div>
@endsection
