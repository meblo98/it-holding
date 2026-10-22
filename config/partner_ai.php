<?php

return [
    // Nombre maximal d'images (générées ou modifiées) par partenaire et par jour.
    'daily_limit' => (int) env('PARTNER_AI_DAILY_LIMIT', 5),

    // Modèle Gemini capable de produire/modifier des images (clé : GEMINI_API_KEY).
    'model' => env('GEMINI_IMAGE_MODEL', 'gemini-2.5-flash-image'),

    // Types de partenaires autorisés.
    'partner_types' => ['revendeur', 'commercial'],
];
