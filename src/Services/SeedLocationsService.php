<?php

namespace Gp10devhts\UgVillageLocations\Services;

use Gp10devhts\UgVillageLocations\Facades\UgVillageLocations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SeedLocationsService
{
    public function seed(?callable $onProgress = null): void
    {
        $configuredLevels = config('ug-village-locations.seed_levels', ['districts', 'counties', 'sub_counties', 'parishes', 'villages']);

        $hierarchy = [
            'districts' => [],
            'counties' => ['districts'],
            'sub_counties' => ['districts', 'counties'],
            'parishes' => ['districts', 'counties', 'sub_counties'],
            'villages' => ['districts', 'counties', 'sub_counties', 'parishes'],
        ];

        $levelsToSeed = [];
        foreach ($configuredLevels as $level) {
            $levelsToSeed[] = $level;
            if (isset($hierarchy[$level])) {
                $levelsToSeed = array_merge($levelsToSeed, $hierarchy[$level]);
            }
        }

        $allLevels = ['districts', 'counties', 'sub_counties', 'parishes', 'villages'];
        $levels = array_intersect($allLevels, array_unique($levelsToSeed));

        $dumpDir = __DIR__.'/../../database/dumps';
        $jsonPath = __DIR__.'/../../resources/data/uganda_locations.json';

        $hasDumps = false;
        foreach ($levels as $level) {
            if (File::exists("{$dumpDir}/{$level}.sql")) {
                $hasDumps = true;
                break;
            }
        }

        if ($hasDumps) {
            $this->seedFromDumps($levels, $dumpDir, $onProgress);
        } elseif (File::exists($jsonPath)) {
            $this->seedFromJson($levels, $jsonPath, $onProgress);
        } else {
            if ($onProgress) {
                $onProgress('No seed data found (SQL dumps or JSON).');
            }
        }
    }

    protected function seedFromDumps(array $levels, string $dumpDir, ?callable $onProgress): void
    {
        DB::transaction(function () use ($levels, $dumpDir, $onProgress) {
            foreach ($levels as $level) {
                $path = "{$dumpDir}/{$level}.sql";
                if (! File::exists($path)) {
                    continue;
                }

                if ($onProgress) {
                    $onProgress("Seeding {$level} from SQL dump...");
                }

                $sql = File::get($path);
                if (! empty(trim($sql))) {
                    DB::unprepared($sql);
                }
            }
        });
    }

    protected function seedFromJson(array $levels, string $jsonPath, ?callable $onProgress): void
    {
        $json = json_decode(File::get($jsonPath), true);
        $data = $json['data'] ?? $json;

        DB::transaction(function () use ($levels, $data, $onProgress) {
            foreach ($levels as $level) {
                if ($onProgress) {
                    $onProgress("Seeding {$level} from JSON...");
                }

                $method = 'seed'.str_replace('_', '', ucwords($level, '_'));
                if (method_exists($this, $method)) {
                    $this->$method($data, $onProgress);
                }
            }
        });
    }

    protected function seedDistricts(array $data, ?callable $onProgress): void
    {
        $model = UgVillageLocations::districtModel();
        foreach ($data as $district) {
            $model::updateOrCreate(['id' => $district['id']], ['name' => $district['name']]);
        }
    }

    protected function seedCounties(array $data, ?callable $onProgress): void
    {
        $model = UgVillageLocations::countyModel();
        foreach ($data as $district) {
            foreach ($district['counties'] ?? [] as $county) {
                $model::updateOrCreate(
                    ['id' => $county['id']],
                    ['district_id' => $district['id'], 'name' => $county['name']]
                );
            }
        }
    }

    protected function seedSubCounties(array $data, ?callable $onProgress): void
    {
        $model = UgVillageLocations::subCountyModel();
        foreach ($data as $district) {
            foreach ($district['counties'] ?? [] as $county) {
                foreach ($county['sub_counties'] ?? [] as $subCounty) {
                    $model::updateOrCreate(
                        ['id' => $subCounty['id']],
                        ['county_id' => $county['id'], 'name' => $subCounty['name']]
                    );
                }
            }
        }
    }

    protected function seedParishes(array $data, ?callable $onProgress): void
    {
        $model = UgVillageLocations::parishModel();
        foreach ($data as $district) {
            foreach ($district['counties'] ?? [] as $county) {
                foreach ($county['sub_counties'] ?? [] as $subCounty) {
                    foreach ($subCounty['parishes'] ?? [] as $parish) {
                        $model::updateOrCreate(
                            ['id' => $parish['id']],
                            ['sub_county_id' => $subCounty['id'], 'name' => $parish['name']]
                        );
                    }
                }
            }
        }
    }

    protected function seedVillages(array $data, ?callable $onProgress): void
    {
        $model = UgVillageLocations::villageModel();
        foreach ($data as $district) {
            if ($onProgress) {
                $onProgress("Seeding villages for district: {$district['name']}");
            }
            foreach ($district['counties'] ?? [] as $county) {
                foreach ($county['sub_counties'] ?? [] as $subCounty) {
                    foreach ($subCounty['parishes'] ?? [] as $parish) {
                        $villages = [];
                        foreach ($parish['villages'] ?? [] as $village) {
                            $villages[] = [
                                'id' => $village['id'],
                                'parish_id' => $parish['id'],
                                'name' => $village['name'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        if (! empty($villages)) {
                            $model::upsert($villages, ['id'], ['parish_id', 'name', 'updated_at']);
                        }
                    }
                }
            }
        }
    }
}
