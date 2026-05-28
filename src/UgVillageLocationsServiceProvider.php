<?php

namespace Gp10devhts\UgVillageLocations;

use Gp10devhts\UgVillageLocations\Commands\UgVillageLocationsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UgVillageLocationsServiceProvider extends PackageServiceProvider
{
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
            ->hasViews()
            ->hasMigration('create_ug_village_locations_table')
            ->hasCommand(UgVillageLocationsCommand::class);
    }
}
