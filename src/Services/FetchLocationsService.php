<?php

namespace Gp10devhts\UgVillageLocations\Services;

use Gp10devhts\UgVillageLocations\Providers\LocationProviderInterface;
use Illuminate\Support\Facades\File;

class FetchLocationsService
{
    protected string $rawPath;
    protected string $finalPath;

    public function __construct(protected LocationProviderInterface $provider)
    {
        $this->rawPath = __DIR__ . '/../../resources/data/raw';
        $this->finalPath = __DIR__ . '/../../resources/data/uganda_locations.json';
    }

    public function fetchAll(bool $fresh = false, ?callable $onProgress = null): void
    {
        if ($fresh) {
            File::deleteDirectory($this->rawPath);
            File::delete($this->finalPath);
        }

        File::ensureDirectoryExists($this->rawPath);

        $districts = $this->provider->getDistricts();
        $totalDistricts = count($districts);

        foreach ($districts as $index => $district) {
            $safeName = strtolower(preg_replace('/[^a-z0-9]/i', '-', $district['name']));
            $fileName = sprintf('%03d-%s.json', $district['id'], $safeName);
            $filePath = $this->rawPath . '/' . $fileName;

            if (File::exists($filePath)) {
                if ($onProgress) {
                    $onProgress('district_skipped', [
                        'name' => $district['name'],
                        'current' => $index + 1,
                        'total' => $totalDistricts
                    ]);
                }
                continue;
            }

            if ($onProgress) {
                $onProgress('district_start', [
                    'name' => $district['name'],
                    'current' => $index + 1,
                    'total' => $totalDistricts
                ]);
            }

            $districtData = $district;
            $districtData['counties'] = [];

            $counties = $this->provider->getCounties($district['id']);
            foreach ($counties as $county) {
                if ($onProgress) {
                    $onProgress('county_start', ['name' => $county['name']]);
                }

                $countyData = $county;
                $countyData['sub_counties'] = [];

                $subCounties = $this->provider->getSubCounties($county['id']);
                foreach ($subCounties as $subCounty) {
                    if ($onProgress) {
                        $onProgress('sub_county_start', ['name' => $subCounty['name']]);
                    }

                    $subCountyData = $subCounty;
                    $subCountyData['parishes'] = [];

                    $parishes = $this->provider->getParishes($subCounty['id']);
                    foreach ($parishes as $parish) {
                        if ($onProgress) {
                            $onProgress('parish_start', ['name' => $parish['name']]);
                        }

                        $parishData = $parish;
                        $villages = $this->provider->getVillages($parish['id']);
                        $parishData['villages'] = $villages;

                        if ($onProgress) {
                            $onProgress('villages_found', ['count' => count($villages)]);
                        }

                        $subCountyData['parishes'][] = $parishData;
                    }
                    $countyData['sub_counties'][] = $subCountyData;
                }
                $districtData['counties'][] = $countyData;
            }

            // Atomic write for district file
            $tmpPath = $filePath . '.tmp';
            File::put($tmpPath, json_encode($districtData, JSON_PRETTY_PRINT));
            File::move($tmpPath, $filePath);

            if ($onProgress) {
                $onProgress('district_completed', ['name' => $district['name']]);
                $onProgress('checkpoint_saved', ['path' => $filePath]);
            }
        }

        $this->mergeAll($onProgress);
    }

    public function mergeAll(?callable $onProgress = null): void
    {
        if ($onProgress) {
            $onProgress('merging_start', []);
        }

        $files = File::glob($this->rawPath . '/*.json');
        sort($files);

        $data = [];
        foreach ($files as $file) {
            $data[] = json_decode(File::get($file), true);
        }

        $metadata = [
            'generated_at' => date('c'),
            'source' => 'passports.go.ug',
            'districts_completed' => count($data),
            'data' => $data,
        ];

        $tmpPath = $this->finalPath . '.tmp';
        File::put($tmpPath, json_encode($metadata, JSON_PRETTY_PRINT));
        File::move($tmpPath, $this->finalPath);

        if ($onProgress) {
            $onProgress('merging_completed', ['path' => $this->finalPath]);
        }
    }
}
