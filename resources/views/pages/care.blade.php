@extends('layouts.app')

@section('title', 'Garantie & Protection - ' . config('app.name'))
@section('meta_description', 'Garantie & Protection IT Holding Services : trois formules annuelles pour réduire vos frais de réparation d’ordinateurs. Prise en charge de 25 %, 50 % ou 75 % des frais admissibles.')

@section('content')
@php
    $fmt = fn ($n) => number_format($n, 0, ',', ' ') . ' FCFA';
    $steps = [
        ['Choisissez une formule', 'Comparez les trois formules et lisez les conditions et les exclusions avant de payer.'],
        ['Enregistrez votre appareil', 'Marque, modèle, numéro de série, état initial et photos. L’admissibilité est vérifiée avant l’activation.'],
        ['Déclarez une panne', 'Décrivez le problème depuis votre espace. Un technicien réalise le diagnostic.'],
        ['Recevez un devis détaillé', 'Vous voyez la part prise en charge et votre reste à payer. Aucune réparation payante ne démarre sans votre accord.'],
    ];
    $covered = [
        'Carte mère et composants électroniques réparables',
        'Clavier, connectique et ports',
        'Ventilateurs et système de refroidissement',
        'Alimentation interne et composants d’alimentation admissibles',
        'Écran en panne technique, sans dommage physique',
        'Diagnostic des pannes matérielles',
    ];
    $excluded = [
        'Pannes présentes avant la souscription et non déclarées',
        'Casse physique, liquides et dommages accidentels',
        'Vol et perte de l’appareil',
        'Utilisation non conforme, modifications ou réparations non autorisées',
        'Consommables, licences logicielles et abonnements',
        'Récupération de données et restauration de fichiers',
        'Remplacement complet de l’appareil',
        'Dommages relevant d’une garantie constructeur ou d’un autre contrat',
    ];
@endphp

<!-- Introduction -->
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
        <p class="text-sm font-semibold text-gold-600 mb-3">{{ config('care.name') }}</p>
        <h1 class="text-4xl lg:text-5xl font-semibold text-navy-900 leading-tight tracking-tight max-w-3xl">
            Une partie de vos frais de réparation prise en charge, pendant 12 mois.
        </h1>
        <p class="mt-6 text-lg text-navy-500 max-w-2xl leading-relaxed">
            Souscrivez un abonnement annuel, enregistrez vos ordinateurs et suivez vos réparations en ligne. Nous prenons en charge 25 %, 50 % ou 75 % des frais admissibles, dans la limite de votre formule.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="#formules" class="btn-primary-gold">Voir les formules</a>
            <a href="{{ route('contact.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-md border border-navy-200 font-semibold text-navy-900 hover:bg-navy-50 transition-colors">Poser une question</a>
        </div>
        <p class="mt-6 text-sm text-navy-400 max-w-2xl">
            Il s’agit d’une garantie commerciale de réparation complémentaire. Elle ne remplace ni la garantie du fabricant, ni vos garanties légales, et n’est pas une assurance.
        </p>
    </div>
</section>

<!-- Formules -->
<section id="formules" class="bg-navy-50 border-y border-navy-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-navy-900 tracking-tight">Trois formules, 12 mois</h2>
        <p class="mt-3 text-navy-500 max-w-2xl">Le prix est annuel. La durée court à partir de la date d’activation indiquée sur votre certificat.</p>

        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($plans as $key => $p)
                <div class="bg-white rounded-md p-6 flex flex-col {{ $p['highlight'] ? 'border-2 border-gold-500' : 'border border-navy-100' }}">
                    @if ($p['highlight'])
                        <span class="self-start text-xs font-semibold bg-gold-500 text-navy-900 px-2 py-1 rounded mb-3">Le plus choisi</span>
                    @endif
                    <h3 class="text-xl font-semibold text-navy-900">{{ $p['label'] }}</h3>
                    <p class="mt-3 text-3xl font-semibold text-navy-900 tracking-tight">{{ $fmt($p['price']) }}<span class="text-base font-normal text-navy-400"> / an</span></p>
                    <p class="mt-4 text-navy-600"><span class="font-semibold text-navy-900">{{ $p['coverage_rate'] }} %</span> des frais admissibles pris en charge</p>
                    <p class="mt-1 text-sm text-navy-500">Vous réglez {{ 100 - $p['coverage_rate'] }} %</p>
                    <ul class="mt-5 space-y-2 text-sm text-navy-600 flex-grow">
                        <li>Plafond par réparation : {{ $fmt($p['per_repair_cap']) }}</li>
                        <li>Plafond sur 12 mois : {{ $fmt($p['per_year_cap']) }}</li>
                        <li>Traitement : {{ $p['priority'] }}</li>
                        <li>Suivi, historique et rapport de réparation inclus</li>
                    </ul>
                    <p class="mt-5 text-sm text-navy-400">{{ $p['target'] }}</p>
                    <a href="{{ route('care.subscribe', $key) }}" class="mt-6 {{ $p['highlight'] ? 'btn-primary-gold justify-center' : 'inline-flex justify-center px-5 py-3 rounded-md border border-navy-200 font-semibold text-navy-900 hover:bg-navy-50 transition-colors' }}">Choisir {{ $p['label'] }}</a>
                </div>
            @endforeach
        </div>
        <p class="mt-6 text-sm text-navy-400 max-w-3xl">
            Les plafonds correspondent au montant maximal pris en charge par IT Holding Services sur les réparations couvertes. Ce n’est pas une somme versée au client. Le nombre d’interventions n’est pas limité tant que les conditions et les plafonds sont respectés.
        </p>
    </div>
</section>

<!-- Comparatif -->
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-navy-900 tracking-tight">Comparer les formules</h2>
        <div class="mt-8 overflow-x-auto">
            <table class="w-full min-w-[34rem] text-left text-sm">
                <thead>
                    <tr class="border-b-2 border-navy-900 text-navy-900">
                        <th class="py-3 pr-4 font-semibold">Avantage</th>
                        @foreach ($plans as $p)<th class="py-3 px-4 font-semibold">{{ $p['label'] }}</th>@endforeach
                    </tr>
                </thead>
                <tbody class="text-navy-600">
                    @php
                        $rows = [
                            ['Prix annuel', fn ($p) => $fmt($p['price'])],
                            ['Prise en charge', fn ($p) => $p['coverage_rate'] . ' %'],
                            ['Part du client', fn ($p) => (100 - $p['coverage_rate']) . ' %'],
                            ['Plafond par réparation', fn ($p) => $fmt($p['per_repair_cap'])],
                            ['Plafond sur 12 mois', fn ($p) => $fmt($p['per_year_cap'])],
                            ['Niveau de traitement', fn ($p) => $p['priority']],
                            ['Remise sur prestations hors couverture', fn ($p) => $p['parts_discount'] . ' %'],
                            ['Remise sur main-d’œuvre hors couverture', fn ($p) => $p['labor_discount'] . ' %'],
                            ['Suivi, historique, rapport de réparation', fn ($p) => 'Inclus'],
                        ];
                    @endphp
                    @foreach ($rows as [$label, $value])
                        <tr class="border-b border-navy-100">
                            <th scope="row" class="py-3 pr-4 font-medium text-navy-900">{{ $label }}</th>
                            @foreach ($plans as $p)<td class="py-3 px-4">{{ $value($p) }}</td>@endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="mt-4 text-sm text-navy-400">Les délais de traitement prioritaires sont des objectifs de service, pas des délais garantis. Les remises ne se cumulent pas sur une même dépense.</p>
    </div>
</section>

<!-- Exemple de calcul -->
<section class="bg-navy-50 border-y border-navy-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-navy-900 tracking-tight">Un exemple concret</h2>
        <p class="mt-3 text-navy-500 max-w-2xl">Pour une réparation admissible de {{ $fmt($example) }}, sans plafond atteint. La prise en charge porte sur les frais admissibles approuvés après diagnostic, pas sur la valeur de l’appareil.</p>
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($plans as $p)
                @php
                    $part = min($example * $p['coverage_rate'] / 100, $p['per_repair_cap']);
                @endphp
                <div class="border-t-2 border-navy-900 pt-5">
                    <h3 class="font-semibold text-navy-900">{{ $p['label'] }}</h3>
                    <p class="mt-2 text-navy-600">IT Holding Services : <span class="font-semibold text-navy-900">{{ $fmt($part) }}</span></p>
                    <p class="text-navy-600">À votre charge : <span class="font-semibold text-navy-900">{{ $fmt($example - $part) }}</span></p>
                </div>
            @endforeach
        </div>
        <p class="mt-6 text-sm text-navy-400 max-w-3xl">Le montant pris en charge ne dépasse jamais le taux de votre formule, le plafond par réparation ni le solde de votre plafond annuel. Le calcul détaillé vous est présenté avant toute décision.</p>
    </div>
</section>

<!-- Fonctionnement -->
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-navy-900 tracking-tight mb-10">Comment ça marche</h2>
        <ol class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            @foreach ($steps as $i => [$title, $text])
                <li class="border-t-2 border-navy-900 pt-5">
                    <span class="text-sm font-semibold text-gold-600">Étape {{ $i + 1 }}</span>
                    <h3 class="mt-1 text-lg font-semibold text-navy-900">{{ $title }}</h3>
                    <p class="mt-2 text-navy-600 leading-relaxed">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

<!-- Couverture et exclusions -->
<section id="couverture" class="bg-navy-50 border-y border-navy-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-navy-900 tracking-tight">Ce qui est couvert, ce qui ne l’est pas</h2>
        <p class="mt-3 text-navy-500 max-w-2xl">Au lancement, la protection concerne les {{ strtolower(implode(' et les ', config('care.pilot_categories'))) }}. Une pièce n’est couverte que si elle figure dans la grille de votre formule : être réparable ne suffit pas.</p>
        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-12">
            <div>
                <h3 class="text-lg font-semibold text-navy-900">Pièces et interventions pouvant être admissibles après diagnostic</h3>
                <ul class="mt-4 space-y-2 text-navy-600">
                    @foreach ($covered as $item)<li class="border-b border-navy-100 pb-2">{{ $item }}</li>@endforeach
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-navy-900">Exclusions principales</h3>
                <ul class="mt-4 space-y-2 text-navy-600">
                    @foreach ($excluded as $item)<li class="border-b border-navy-100 pb-2">{{ $item }}</li>@endforeach
                </ul>
            </div>
        </div>
        <p class="mt-6 text-sm text-navy-400 max-w-3xl">Les imprimantes, onduleurs, équipements réseau et caméras pourront être ajoutés ensuite. Les exclusions ne suppriment pas vos droits au titre des garanties légales applicables.</p>
    </div>
</section>

<!-- Appel final -->
<section class="bg-navy-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-semibold tracking-tight max-w-xl">Une question avant de souscrire ?</h2>
        <p class="mt-3 text-white/70 max-w-xl">Écrivez-nous ou appelez-nous, nous vous aidons à choisir la formule adaptée à votre usage.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('contact.index') }}" class="btn-primary-gold">Nous écrire</a>
            <a href="tel:+221773518716" class="inline-flex items-center px-5 py-2.5 rounded-md border border-white/30 font-semibold hover:bg-white/10 transition-colors">+221 77 351 87 16</a>
        </div>
    </div>
</section>
@endsection
