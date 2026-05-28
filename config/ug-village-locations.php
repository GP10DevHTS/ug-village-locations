<?php

use Gp10devhts\UgVillageLocations\Models\County;
use Gp10devhts\UgVillageLocations\Models\District;
use Gp10devhts\UgVillageLocations\Models\Parish;
use Gp10devhts\UgVillageLocations\Models\SubCounty;
use Gp10devhts\UgVillageLocations\Models\Village;

return [
    /*
    |--------------------------------------------------------------------------
    | Seed Levels
    |--------------------------------------------------------------------------
    |
    | Define the depth of the administrative hierarchy to seed.
    | Options: 'districts', 'counties', 'sub_counties', 'parishes', 'villages'
    |
    */
    'seed_levels' => [
        'districts',
        'counties',
        'sub_counties',
        'parishes',
        'villages',
    ],

    /*
    |--------------------------------------------------------------------------
    | UUID Support
    |--------------------------------------------------------------------------
    |
    | If set to true, a unique 'uuid' column will be added to each table
    | and automatically populated during seeding.
    |
    */
    'use_uuids' => false,

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Define the model classes to be used for the administrative hierarchy.
    | You can override these classes to add your own logic or relationships.
    |
    */
    'models' => [
        'district' => District::class,
        'county' => County::class,
        'sub_county' => SubCounty::class,
        'parish' => Parish::class,
        'village' => Village::class,
    ],
];
