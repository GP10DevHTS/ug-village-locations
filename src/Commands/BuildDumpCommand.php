<?php

namespace Gp10devhts\UgVillageLocations\Commands;

use Gp10devhts\UgVillageLocations\Services\BuildDumpService;
use Illuminate\Console\Command;

class BuildDumpCommand extends Command
{
    protected $signature = 'ug-locations:build-dump';

    protected $description = 'Build SQL dumps from collected raw data';

    public function handle(): int
    {
        $this->info('Building SQL dumps...');

        $service = new BuildDumpService;
        $service->build(fn ($msg) => $this->line($msg));

        $this->info('SQL dumps generated successfully in database/dumps/');

        return self::SUCCESS;
    }
}
