<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Livraison automatique : clôture des appels d'offres dont le délai est dépassé.
Schedule::command('delivery:tick')->everyMinute()->withoutOverlapping();
