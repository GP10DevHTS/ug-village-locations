<?php

namespace Gp10devhts\UgVillageLocations\Commands;

use Gp10devhts\UgVillageLocations\Services\SeedLocationsService;
use Illuminate\Console\Command;

class SeedLocationsCommand extends Command
{
    protected $signature = 'ug-locations:seed';
    protected $description = 'Seed Uganda administrative data from local dumps';

    public function handle(): int
    {
        $this->info('Seeding Uganda administrative data...');

        $service = new SeedLocationsService();
        $service->seed(fn($msg) => $this->line($msg));

        $this->info('Seeding complete.');

        return self::SUCCESS;
    }
}
