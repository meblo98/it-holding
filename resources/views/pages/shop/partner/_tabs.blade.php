{{-- Onglets de l'espace partenaire. Attend : $active (clé de l'onglet courant), $extraClass optionnel. --}}
@php
    $tabUser = auth()->user();
    $tabs = [
        'dashboard'     => ['route' => 'dashboard.partner',               'icon' => '📊', 'label' => 'Tableau de bord'],
        'crm'           => ['route' => 'dashboard.partner.crm',           'icon' => '👥', 'label' => 'CRM & Prospects'],
        'opportunities' => ['route' => 'dashboard.partner.opportunities', 'icon' => '🎯', 'label' => 'Opportunités'],
    ];
    if ($tabUser && in_array($tabUser->partner_type, \App\Models\Mission::ELIGIBLE_PARTNER_TYPES, true)) {
        $tabs['missions'] = ['route' => 'dashboard.partner.missions', 'icon' => '💼', 'label' => 'Missions'];
    }
    $tabs += [
        'wallet'    => ['route' => 'dashboard.partner.wallet',    'icon' => '💰', 'label' => 'Portefeuille'],
        'profile'   => ['route' => 'dashboard.partner.profile',   'icon' => '🪪', 'label' => 'Mon profil pro'],
        'assistant' => ['route' => 'dashboard.partner.assistant', 'icon' => '🤖', 'label' => 'Assistant IA'],
        'marketing' => ['route' => 'dashboard.partner.marketing', 'icon' => '📢', 'label' => 'Studio Marketing'],
        'contract'  => ['route' => 'dashboard.partner.contract',  'icon' => '📄', 'label' => 'Contrat'],
    ];
@endphp
<div class="flex overflow-x-auto border-b border-gray-200 bg-white rounded-xl p-2 shadow-sm gap-2 scrollbar-none whitespace-nowrap {{ $extraClass ?? '' }}">
    @foreach($tabs as $key => $tab)
        <a href="{{ route($tab['route']) }}" class="shrink-0 px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors flex items-center gap-2 {{ ($active ?? '') === $key ? 'bg-navy-900 text-white' : 'text-gray-500 hover:text-navy-900 hover:bg-gray-50' }}">
            <span>{{ $tab['icon'] }}</span> {{ $tab['label'] }}
        </a>
    @endforeach
</div>
