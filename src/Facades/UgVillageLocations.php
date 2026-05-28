<?php

namespace Gp10devhts\UgVillageLocations\Facades;

use Gp10devhts\UgVillageLocations\UgLocations;
use Illuminate\Support\Facades\Facade;

/**
 * @see UgLocations
 */
class UgVillageLocations extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ug-locations';
    }
}
