<?php

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
        'district' => \Gp10devhts\UgVillageLocations\Models\District::class,
        'county' => \Gp10devhts\UgVillageLocations\Models\County::class,
        'sub_county' => \Gp10devhts\UgVillageLocations\Models\SubCounty::class,
        'parish' => \Gp10devhts\UgVillageLocations\Models\Parish::class,
        'village' => \Gp10devhts\UgVillageLocations\Models\Village::class,
    ],
];
