# Uganda Administrative Hierarchy Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gp10devhts/ug-village-locations.svg?style=flat-square)](https://packagist.org/packages/gp10devhts/ug-village-locations)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/gp10devhts/ug-village-locations/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/gp10devhts/ug-village-locations/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/gp10devhts/ug-village-locations.svg?style=flat-square)](https://packagist.org/packages/gp10devhts/ug-village-locations)

A production-ready Laravel package that provides Uganda administrative locations from District → County → Sub County → Parish → Village.

## Features

- Full administrative hierarchy: Districts, Counties, Sub-Counties, Parishes, and Villages.
- Fast seeding via SQL dumps (offline support).
- Configurable hierarchy depth.
- Eloquent models and relationships.
- Name-based searching scopes.
- Maintainer tools for data collection from remote sources.
- Optional UUID support.

## Installation

You can install the package via composer:

```bash
composer require gp10devhts/ug-village-locations
```

Publish the config and migrations:

```bash
php artisan vendor:publish --tag="ug-village-locations-config"
php artisan vendor:publish --tag="ug-village-locations-migrations"
```

Run the migrations:

```bash
php artisan migrate
```

Seed the locations:

```bash
php artisan ug-locations:seed
```

## Configuration

You can customize the package via `config/ug-village-locations.php`:

```php
return [
    'seed_levels' => [
        'districts',
        'counties',
        'sub_counties',
        'parishes',
        'villages',
    ],
    'use_uuids' => false,

    'models' => [
        'district' => \Gp10devhts\UgVillageLocations\Models\District::class,
        'county' => \Gp10devhts\UgVillageLocations\Models\County::class,
        'sub_county' => \Gp10devhts\UgVillageLocations\Models\SubCounty::class,
        'parish' => \Gp10devhts\UgVillageLocations\Models\Parish::class,
        'village' => \Gp10devhts\UgVillageLocations\Models\Village::class,
    ],
];
```

## Usage

### Eloquent Models

```php
use Gp10devhts\UgVillageLocations\Models\District;
use Gp10devhts\UgVillageLocations\Models\Village;

// Get all districts
$districts = District::all();

// Search by name
$kampala = District::search('Kampala')->first();

// Relationships
$counties = $kampala->counties;
$villages = Village::where('name', 'like', '%Kibuli%')->with('parish.subCounty.county.district')->get();

// Using the Facade for model resolution and helper methods
use Gp10devhts\UgVillageLocations\Facades\UgVillageLocations;

$districtModel = UgVillageLocations::districtModel();
$districts = UgVillageLocations::districts();
$counties = UgVillageLocations::counties($districtId);
```

### Demo Project
A demo Laravel project is available at [github.com/GP10DevHTS/ug-locations-demo](https://github.com/GP10DevHTS/ug-locations-demo).

#### **Notices:**
- the project uses a custom District model to add regions.
- the model extention migrations use the default table names from the package.

### Model Extensibility

You can override the default models by updating the `models` array in the config file. This allows you to add custom relationships, scopes, or traits.

```php
// config/ug-village-locations.php
'models' => [
    'village' => App\Models\Village::class,
],
```

Your custom model should extend the package's base model:

```php
namespace App\Models;

use Gp10devhts\UgVillageLocations\Models\Village as BaseVillage;

class Village extends BaseVillage
{
    // Custom logic
}
```

## Artisan Commands

- `php artisan ug-locations:seed`: Seed the database from local SQL dumps.
- `php artisan ug-locations:truncate`: Wipe all administrative location data.
- `php artisan ug-locations:fetch`: (Maintainer only) Fetch fresh data from remote source.
- `php artisan ug-locations:build-dump`: (Maintainer only) Generate SQL dumps from fetched data.

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
