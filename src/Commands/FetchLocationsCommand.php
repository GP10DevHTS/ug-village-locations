<?php

namespace Gp10devhts\UgVillageLocations\Commands;

use Gp10devhts\UgVillageLocations\Providers\PassportUgProvider;
use Gp10devhts\UgVillageLocations\Services\FetchLocationsService;
use Illuminate\Console\Command;

class FetchLocationsCommand extends Command
{
    protected $signature = 'ug-locations:fetch {--fresh : Clear existing data and start over}';
    protected $description = 'Fetch all Uganda administrative data from remote source';

    public function handle(): int
    {
        $this->info('Starting data collection from remote source...');

        $provider = new PassportUgProvider();
        $service = new FetchLocationsService($provider);

        $service->fetchAll($this->option('fresh'), function($event, $data) {
            switch ($event) {
                case 'district_skipped':
                    $this->line("<info>Skipping district:</info> {$data['name']} ({$data['current']}/{$data['total']}) - already fetched.");
                    break;
                case 'district_start':
                    $this->newLine();
                    $this->line("<info>Fetching district:</info> {$data['name']} ({$data['current']}/{$data['total']})");
                    break;
                case 'county_start':
                    $this->line("  Fetching county: {$data['name']}");
                    break;
                case 'sub_county_start':
                    $this->line("    Fetching subcounty: {$data['name']}");
                    break;
                case 'parish_start':
                    $this->line("      Fetching parish: {$data['name']}");
                    break;
                case 'villages_found':
                    $this->line("        Found {$data['count']} villages");
                    break;
                case 'district_completed':
                    $this->info("Completed district: {$data['name']}");
                    break;
                case 'checkpoint_saved':
                    $this->comment("Saved checkpoint.");
                    break;
                case 'merging_start':
                    $this->info("Merging all district data into final dataset...");
                    break;
                case 'merging_completed':
                    $this->info("Final dataset generated: {$data['path']}");
                    break;
            }
        });

        $this->info('Data collection complete.');

        return self::SUCCESS;
    }
}
