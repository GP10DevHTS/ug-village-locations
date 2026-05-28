<?php

namespace Gp10devhts\UgVillageLocations\Commands;

use Illuminate\Console\Command;

class UgVillageLocationsCommand extends Command
{
    public $signature = 'ug-village-locations';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
