<?php

namespace Gp10devhts\UgVillageLocations\Facades;

use Gp10devhts\UgVillageLocations\UgLocations;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string districtModel()
 * @method static string countyModel()
 * @method static string subCountyModel()
 * @method static string parishModel()
 * @method static string villageModel()
 * @method static \Illuminate\Support\Collection districts()
 * @method static \Illuminate\Support\Collection counties(?int $districtId = null)
 * @method static \Illuminate\Support\Collection subCounties(?int $countyId = null)
 * @method static \Illuminate\Support\Collection parishes(?int $subCountyId = null)
 * @method static \Illuminate\Support\Collection villages(?int $parishId = null)
 *
 * @see UgLocations
 */
class UgVillageLocations extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ug-locations';
    }
}
