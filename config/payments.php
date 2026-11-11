<?php

return [
    'wave' => [
        // Lien de paiement marchand Wave. Modifiable dans le .env : WAVE_PAYMENT_LINK=...
        'link' => env('WAVE_PAYMENT_LINK', 'https://pay.wave.com/m/M_sn_ArxLeO1zVOZ2/c/sn/'),
    ],
];
