<?php

namespace App\Console\Commands;

use App\Services\SourcingService;
use Illuminate\Console\Command;

class DeliveryTick extends Command
{
    protected $signature = 'delivery:tick';

    protected $description = 'Clôture les appels d\'offres dont le délai est dépassé et retient la meilleure offre.';

    public function handle(): int
    {
        $closed = SourcingService::tick();
        $this->info("Appels d'offres traités : {$closed}");

        return self::SUCCESS;
    }
}
