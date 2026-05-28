<?php

namespace Gp10devhts\UgVillageLocations\Services;

use Gp10devhts\UgVillageLocations\Providers\LocationProviderInterface;
use Illuminate\Support\Facades\File;

class FetchLocationsService
{
    public function __construct(protected LocationProviderInterface $provider) {}

    public function fetchAll(?callable $onProgress = null): array
    {
        $data = [];
        $districts = $this->provider->getDistricts();
        $totalDistricts = count($districts);

        foreach ($districts as $index => $district) {
            if ($onProgress) {
                $onProgress("Fetching district: {$district['name']} (".($index + 1)."/{$totalDistricts})");
            }

            $districtData = $district;
            $districtData['counties'] = [];

            $counties = $this->provider->getCounties($district['id']);
            foreach ($counties as $county) {
                $countyData = $county;
                $countyData['sub_counties'] = [];

                $subCounties = $this->provider->getSubCounties($county['id']);
                foreach ($subCounties as $subCounty) {
                    $subCountyData = $subCounty;
                    $subCountyData['parishes'] = [];

                    $parishes = $this->provider->getParishes($subCounty['id']);
                    foreach ($parishes as $parish) {
                        $parishData = $parish;
                        $parishData['villages'] = $this->provider->getVillages($parish['id']);
                        $subCountyData['parishes'][] = $parishData;
                    }
                    $countyData['sub_counties'][] = $subCountyData;
                }
                $districtData['counties'][] = $countyData;
            }
            $data[] = $districtData;
        }

        $path = __DIR__.'/../../resources/data/uganda_locations.json';
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($data, JSON_PRETTY_PRINT));

        return $data;
    }
}
