{{-- Onglets de l'espace partenaire. Attend : $active (clé de l'onglet courant), $extraClass optionnel.
     Chaque catégorie de partenaire (commercial, apporteur, revendeur) a son propre jeu d'onglets.
     Les autres catégories (comptes existants) gardent l'ensemble d'origine. --}}
@php
    $tabUser = auth()->user();
    $type = $tabUser?->partner_type;

    $all = [
        'dashboard'     => ['route' => 'dashboard.partner',               'label' => 'Tableau de bord'],
        'catalog'       => ['route' => 'dashboard.partner.catalog',       'label' => 'Catalogue'],
        'crm'           => ['route' => 'dashboard.partner.crm',           'label' => 'Prospects'],
        'opportunities' => ['route' => 'dashboard.partner.opportunities', 'label' => 'Opportunités'],
        'missions'      => ['route' => 'dashboard.partner.missions',      'label' => 'Missions'],
        'marketing'     => ['route' => 'dashboard.partner.marketing',     'label' => 'Studio marketing'],
        'assistant'     => ['route' => 'dashboard.partner.assistant',     'label' => 'Assistant IA'],
        'wallet'        => ['route' => 'dashboard.partner.wallet',        'label' => 'Portefeuille'],
        'profile'       => ['route' => 'dashboard.partner.profile',       'label' => 'Mon profil pro'],
        'contract'      => ['route' => 'dashboard.partner.contract',      'label' => 'Contrat'],
    ];

    $sets = [
        'commercial' => ['dashboard', 'catalog', 'crm', 'marketing', 'assistant', 'wallet', 'contract'],
        'apporteur'  => ['dashboard', 'opportunities', 'wallet', 'contract'],
        'revendeur'  => ['dashboard', 'catalog', 'marketing', 'wallet', 'contract'],
    ];

    if (isset($sets[$type])) {
        $keys = $sets[$type];
    } else {
        // Comptes existants (freelance, prestataire, créateur…) : ensemble d'origine.
        $keys = ['dashboard', 'crm', 'opportunities'];
        if ($tabUser && in_array($type, \App\Models\Mission::ELIGIBLE_PARTNER_TYPES, true)) {
            $keys[] = 'missions';
        }
        $keys = array_merge($keys, ['wallet', 'profile', 'assistant', 'marketing', 'contract']);
    }
    $tabs = array_intersect_key($all, array_flip($keys));
    $tabs = array_replace(array_flip($keys), $tabs);
@endphp
<nav aria-label="Espace partenaire" class="flex overflow-x-auto bg-white border border-navy-100 rounded-lg p-1.5 gap-1 whitespace-nowrap {{ $extraClass ?? '' }}">
    @foreach($tabs as $key => $tab)
        <a href="{{ route($tab['route']) }}"
           class="shrink-0 px-4 py-2.5 rounded-md text-[15px] font-semibold transition-colors {{ ($active ?? '') === $key ? 'bg-navy-900 text-white' : 'text-navy-700 hover:bg-navy-50' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</nav>
