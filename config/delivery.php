<?php

/**
 * Livraison automatique : appel d'offres aux fournisseurs, choix de la meilleure offre, attribution à un livreur.
 */
return [
    // Poids de chaque critère dans le choix de la meilleure offre (total = 1).
    // Ordre d'importance retenu : prix > délai > proximité > fiabilité.
    'weights' => [
        'price'       => 0.40,
        'delay'       => 0.30,
        'proximity'   => 0.20,
        'reliability' => 0.10,
    ],

    // Durée pendant laquelle les fournisseurs peuvent répondre à une demande.
    'sourcing_minutes' => 15,

    // Durée laissée à un livreur pour accepter une course avant de passer au suivant.
    'courier_offer_minutes' => 10,

    // Rayon de service par défaut (km) d'un fournisseur ou d'un livreur sans réglage.
    'default_radius_km' => 30,

    // Positions approximatives des villes (utilisées si le client n'a pas partagé sa position GPS).
    'cities' => [
        'dakar'       => [14.7167, -17.4677],
        'pikine'      => [14.7549, -17.3985],
        'guediawaye'  => [14.7769, -17.3883],
        'rufisque'    => [14.7167, -17.2733],
        'thies'       => [14.7910, -16.9359],
        'mbour'       => [14.4198, -16.9640],
        'saly'        => [14.4500, -17.0000],
        'saint-louis' => [16.0179, -16.4896],
        'louga'       => [15.6144, -16.2246],
        'richard-toll' => [16.4625, -15.7006],
        'touba'       => [14.8500, -15.8833],
        'diourbel'    => [14.6550, -16.2310],
        'kaolack'     => [14.1470, -16.0730],
        'fatick'      => [14.3390, -16.4110],
        'kolda'       => [12.8833, -14.9500],
        'ziguinchor'  => [12.5833, -16.2719],
        'tambacounda' => [13.7707, -13.6673],
    ],
];
