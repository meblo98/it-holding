<?php

/**
 * Garantie & Protection IT Holding Services — paramètres des formules.
 * Source unique pour la page publique, le moteur de calcul (étape suivante) et l'admin.
 *
 * ⚠ Les plafonds (per_repair / per_year) et les avantages complémentaires sont des
 * paramètres PROPOSÉS : à valider par une simulation financière avant mise en vente.
 */
return [
    'name'            => 'Garantie & Protection IT Holding Services',
    'duration_months' => 12,
    'currency'        => 'FCFA',

    'plans' => [
        'basique' => [
            'label'            => 'Basique',
            'price'            => 15000,
            'coverage_rate'    => 25,
            'per_repair_cap'   => 15000,
            'per_year_cap'     => 30000,
            'priority'         => 'Standard',
            'parts_discount'   => 5,   // % sur prestations hors couverture (proposé)
            'labor_discount'   => 10,  // % sur main-d'œuvre hors couverture (proposé)
            'target'           => 'Particuliers et utilisateurs qui veulent limiter une partie de leurs dépenses de maintenance.',
            'highlight'        => false,
        ],
        'premium' => [
            'label'            => 'Premium',
            'price'            => 22000,
            'coverage_rate'    => 50,
            'per_repair_cap'   => 30000,
            'per_year_cap'     => 60000,
            'priority'         => 'Prioritaire',
            'parts_discount'   => 10,
            'labor_discount'   => 20,
            'target'           => 'Utilisateurs réguliers, étudiants, indépendants et professionnels.',
            'highlight'        => true,
        ],
        'business' => [
            'label'            => 'Business',
            'price'            => 43000,
            'coverage_rate'    => 75,
            'per_repair_cap'   => 60000,
            'per_year_cap'     => 120000,
            'priority'         => 'Priorité renforcée',
            'parts_discount'   => 15,
            'labor_discount'   => 30,
            'target'           => 'Professionnels et entreprises qui ont besoin d’un suivi renforcé de leurs équipements.',
            'highlight'        => false,
        ],
    ],

    // Lancement pilote : ordinateurs portables et fixes uniquement.
    'pilot_categories' => ['Ordinateurs portables', 'Ordinateurs fixes'],

    // Exemple pédagogique affiché sur la page publique.
    'example_amount' => 40000,
];
