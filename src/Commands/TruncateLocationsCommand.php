<?php

namespace Gp10devhts\UgVillageLocations\Commands;

use Gp10devhts\UgVillageLocations\Services\TruncateLocationsService;
use Illuminate\Console\Command;

class TruncateLocationsCommand extends Command
{
    protected $signature = 'ug-locations:truncate {--level= : The level to truncate}';

    protected $description = 'Clear Uganda administrative location tables';

    public function handle(): int
    {
        $level = $this->option('level');

        if ($level && ! $this->confirm("Are you sure you want to truncate {$level}?")) {
            return self::FAILURE;
        }

        if (! $level && ! $this->confirm('Are you sure you want to truncate ALL Uganda location tables?')) {
            return self::FAILURE;
        }

        $this->info('Truncating tables...');

        $service = new TruncateLocationsService;
        $service->truncate($level, fn ($msg) => $this->line($msg));

        $this->info('Truncation complete.');

        return self::SUCCESS;
    }
}
