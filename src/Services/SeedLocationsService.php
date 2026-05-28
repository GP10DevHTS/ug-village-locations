<?php

namespace Gp10devhts\UgVillageLocations\Services;

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

        $dumpDir = __DIR__ . '/../../database/dumps';

        DB::transaction(function () use ($levels, $dumpDir, $onProgress) {
            foreach ($levels as $level) {
                $path = "{$dumpDir}/{$level}.sql";
                if (!File::exists($path)) {
                    continue;
                }

                if ($onProgress) {
                    $onProgress("Seeding {$level}...");
                }

                $sql = File::get($path);
                if (!empty(trim($sql))) {
                    DB::unprepared($sql);
                }
            }
        });
    }
}
