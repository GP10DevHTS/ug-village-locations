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
];
