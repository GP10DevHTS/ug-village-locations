<?php

namespace Gp10devhts\UgVillageLocations\Services;

use Gp10devhts\UgVillageLocations\Facades\UgVillageLocations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateLocationsService
{
    public function truncate(?string $level = null, ?callable $onProgress = null): void
    {
        $tables = [
            'villages' => $this->resolveTableFromModel(UgVillageLocations::villageModel()),
            'parishes' => $this->resolveTableFromModel(UgVillageLocations::parishModel()),
            'sub_counties' => $this->resolveTableFromModel(UgVillageLocations::subCountyModel()),
            'counties' => $this->resolveTableFromModel(UgVillageLocations::countyModel()),
            'districts' => $this->resolveTableFromModel(UgVillageLocations::districtModel()),
        ];

        Schema::disableForeignKeyConstraints();

        if ($level && isset($tables[$level])) {
            if ($onProgress) {
                $onProgress("Truncating {$tables[$level]}...");
            }
            DB::table($tables[$level])->delete();
        } else {
            foreach ($tables as $name => $tableName) {
                if ($onProgress) {
                    $onProgress("Truncating {$tableName}...");
                }
                DB::table($tableName)->delete();
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    protected function resolveTableFromModel(string $modelClass): string
    {
        return (new $modelClass)->getTable();
    }
}
