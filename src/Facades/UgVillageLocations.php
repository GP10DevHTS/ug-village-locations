<?php

namespace Gp10devhts\UgVillageLocations\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Gp10devhts\UgVillageLocations\UgVillageLocations
 */
class UgVillageLocations extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Gp10devhts\UgVillageLocations\UgVillageLocations::class;
    }
}
