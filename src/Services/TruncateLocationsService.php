<?php

namespace Gp10devhts\UgVillageLocations\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateLocationsService
{
    public function truncate(?string $level = null, ?callable $onProgress = null): void
    {
        $tables = [
            'villages' => 'ug_villages',
            'parishes' => 'ug_parishes',
            'sub_counties' => 'ug_sub_counties',
            'counties' => 'ug_counties',
            'districts' => 'ug_districts',
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
}
