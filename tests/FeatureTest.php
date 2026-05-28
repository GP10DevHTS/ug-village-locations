<?php

use Gp10devhts\UgVillageLocations\Models\District;
use Gp10devhts\UgVillageLocations\Models\County;
use Gp10devhts\UgVillageLocations\Models\SubCounty;
use Gp10devhts\UgVillageLocations\Models\Parish;
use Gp10devhts\UgVillageLocations\Models\Village;
use Gp10devhts\UgVillageLocations\Services\SeedLocationsService;
use Gp10devhts\UgVillageLocations\Services\TruncateLocationsService;
use Gp10devhts\UgVillageLocations\Services\BuildDumpService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    // Clear dumps and data for tests
    File::deleteDirectory(__DIR__ . '/../database/dumps');
    File::deleteDirectory(__DIR__ . '/../resources/data');
});

it('can migrate the tables', function () {
    expect(Schema::hasTable('ug_districts'))->toBeTrue();
    expect(Schema::hasTable('ug_counties'))->toBeTrue();
    expect(Schema::hasTable('ug_sub_counties'))->toBeTrue();
    expect(Schema::hasTable('ug_parishes'))->toBeTrue();
    expect(Schema::hasTable('ug_villages'))->toBeTrue();
});

it('can seed from dumps', function () {
    // Manually create a small dump for testing
    $dumpDir = __DIR__ . '/../database/dumps';
    File::ensureDirectoryExists($dumpDir);

    $districtSql = "INSERT INTO `ug_districts` (`id`, `name`, `created_at`, `updated_at`) VALUES (1, 'TEST DISTRICT', '2023-01-01 00:00:00', '2023-01-01 00:00:00');";
    File::put($dumpDir . '/districts.sql', $districtSql);

    $seeder = new SeedLocationsService();
    $seeder->seed();

    expect(District::count())->toBe(1);
    expect(District::first()->name)->toBe('TEST DISTRICT');
});

it('can truncate tables', function () {
    District::create(['id' => 1, 'name' => 'TEST']);

    $truncator = new TruncateLocationsService();
    $truncator->truncate();

    expect(District::count())->toBe(0);
});

it('can search models', function () {
    District::create(['id' => 1, 'name' => 'Kampala']);
    District::create(['id' => 2, 'name' => 'Wakiso']);

    expect(District::search('Kamp')->count())->toBe(1);
    expect(District::search('Kamp')->first()->id)->toBe(1);
});

it('respects seed_levels config', function () {
    $dumpDir = __DIR__ . '/../database/dumps';
    File::ensureDirectoryExists($dumpDir);
    File::put($dumpDir . '/districts.sql', "INSERT INTO `ug_districts` (`id`, `name`, `created_at`, `updated_at`) VALUES (1, 'D1', '2023-01-01 00:00:00', '2023-01-01 00:00:00');");
    File::put($dumpDir . '/counties.sql', "INSERT INTO `ug_counties` (`id`, `district_id`, `name`, `created_at`, `updated_at`) VALUES (1, 1, 'C1', '2023-01-01 00:00:00', '2023-01-01 00:00:00');");

    config(['ug-village-locations.seed_levels' => ['districts']]);

    $seeder = new SeedLocationsService();
    $seeder->seed();

    expect(District::count())->toBe(1);
    expect(County::count())->toBe(0);
});

it('can use custom models', function () {
    class CustomDistrict extends District {}

    config(['ug-village-locations.models.district' => CustomDistrict::class]);

    expect(\Gp10devhts\UgVillageLocations\Facades\UgVillageLocations::districtModel())->toBe(CustomDistrict::class);

    District::create(['id' => 1, 'name' => 'D1']);

    $district = \Gp10devhts\UgVillageLocations\Facades\UgVillageLocations::districts()->first();
    expect($district)->toBeInstanceOf(CustomDistrict::class);
});
