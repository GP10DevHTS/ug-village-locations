<?php

namespace Gp10devhts\UgVillageLocations\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Gp10devhts\UgVillageLocations\UgLocations
 */
class UgVillageLocations extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ug-locations';
    }
}
