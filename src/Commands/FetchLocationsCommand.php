<?php

namespace Gp10devhts\UgVillageLocations\Commands;

use Gp10devhts\UgVillageLocations\Providers\PassportUgProvider;
use Gp10devhts\UgVillageLocations\Services\FetchLocationsService;
use Illuminate\Console\Command;

class FetchLocationsCommand extends Command
{
    protected $signature = 'ug-locations:fetch';

    protected $description = 'Fetch all Uganda administrative data from remote source';

    public function handle(): int
    {
        $this->info('Starting data collection from remote source...');

        $provider = new PassportUgProvider;
        $service = new FetchLocationsService($provider);

        $service->fetchAll(fn ($msg) => $this->line($msg));

        $this->info('Data collection complete. Raw data saved to resources/data/uganda_locations.json');

        return self::SUCCESS;
    }
}
