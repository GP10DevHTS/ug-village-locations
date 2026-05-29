<?php

use Gp10devhts\UgVillageLocations\Models\District;
use Gp10devhts\UgVillageLocations\Models\County;
use Gp10devhts\UgVillageLocations\Models\SubCounty;
use Gp10devhts\UgVillageLocations\Models\Parish;
use Gp10devhts\UgVillageLocations\Models\Village;
use Gp10devhts\UgVillageLocations\Services\SeedLocationsService;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::deleteDirectory(__DIR__.'/../database/dumps');
    File::deleteDirectory(__DIR__.'/../resources/data');
});

it('can seed from json when dumps are missing', function () {
    $dataDir = __DIR__.'/../resources/data';
    File::ensureDirectoryExists($dataDir);

    $testData = [
        'data' => [
            [
                'id' => 1,
                'name' => 'TEST DISTRICT',
                'counties' => [
                    [
                        'id' => 2,
                        'name' => 'TEST COUNTY',
                        'sub_counties' => [
                            [
                                'id' => 3,
                                'name' => 'TEST SUBCOUNTY',
                                'parishes' => [
                                    [
                                        'id' => 4,
                                        'name' => 'TEST PARISH',
                                        'villages' => [
                                            [
                                                'id' => 5,
                                                'name' => 'TEST VILLAGE'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    File::put($dataDir.'/uganda_locations.json', json_encode($testData));

    $seeder = new SeedLocationsService;
    $seeder->seed();

    expect(District::count())->toBe(1);
    expect(District::find(1)->name)->toBe('TEST DISTRICT');

    expect(County::count())->toBe(1);
    expect(County::find(2)->name)->toBe('TEST COUNTY');

    expect(SubCounty::count())->toBe(1);
    expect(SubCounty::find(3)->name)->toBe('TEST SUBCOUNTY');

    expect(Parish::count())->toBe(1);
    expect(Parish::find(4)->name)->toBe('TEST PARISH');

    expect(Village::count())->toBe(1);
    expect(Village::find(5)->name)->toBe('TEST VILLAGE');
});

it('prefers dumps over json', function () {
    $dumpDir = __DIR__.'/../database/dumps';
    File::ensureDirectoryExists($dumpDir);
    File::put($dumpDir.'/districts.sql', "INSERT INTO `ug_districts` (`id`, `name`, `created_at`, `updated_at`) VALUES (1, 'DUMP DISTRICT', '2023-01-01 00:00:00', '2023-01-01 00:00:00');");

    $dataDir = __DIR__.'/../resources/data';
    File::ensureDirectoryExists($dataDir);
    $testData = ['data' => [['id' => 1, 'name' => 'JSON DISTRICT']]];
    File::put($dataDir.'/uganda_locations.json', json_encode($testData));

    $seeder = new SeedLocationsService;
    $seeder->seed();

    expect(District::count())->toBe(1);
    expect(District::first()->name)->toBe('DUMP DISTRICT');
});
