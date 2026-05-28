<?php

namespace Gp10devhts\UgVillageLocations;

use Gp10devhts\UgVillageLocations\Commands\BuildDumpCommand;
use Gp10devhts\UgVillageLocations\Commands\FetchLocationsCommand;
use Gp10devhts\UgVillageLocations\Commands\SeedLocationsCommand;
use Gp10devhts\UgVillageLocations\Commands\TruncateLocationsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UgVillageLocationsServiceProvider extends PackageServiceProvider
{
    public function packageRegistered(): void
    {
        $this->app->singleton('ug-locations', function () {
            return new UgLocations;
        });
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('ug-village-locations')
            ->hasConfigFile()
            ->hasMigrations([
                'create_ug_districts_table',
                'create_ug_counties_table',
                'create_ug_sub_counties_table',
                'create_ug_parishes_table',
                'create_ug_villages_table',
            ])
            ->hasCommands([
                FetchLocationsCommand::class,
                BuildDumpCommand::class,
                SeedLocationsCommand::class,
                TruncateLocationsCommand::class,
            ]);
    }
}
