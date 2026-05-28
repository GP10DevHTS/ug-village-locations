<?php

namespace Gp10devhts\UgVillageLocations\Tests;

use Gp10devhts\UgVillageLocations\UgVillageLocationsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Gp10devhts\\UgVillageLocations\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            UgVillageLocationsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migrations = [
            'create_ug_districts_table',
            'create_ug_counties_table',
            'create_ug_sub_counties_table',
            'create_ug_parishes_table',
            'create_ug_villages_table',
        ];

        foreach ($migrations as $migration) {
            $m = include __DIR__."/../database/migrations/{$migration}.php.stub";
            $m->up();
        }
    }
}
