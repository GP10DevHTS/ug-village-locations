<?php

namespace Gp10devhts\UgVillageLocations\Services;

use Illuminate\Support\Facades\File;

class BuildDumpService
{
    public function build(?callable $onProgress = null): void
    {
        $jsonPath = __DIR__ . '/../../resources/data/uganda_locations.json';
        if (!File::exists($jsonPath)) {
            throw new \Exception("Raw data file not found. Run fetch command first.");
        }

        $json = json_decode(File::get($jsonPath), true);
        $data = $json['data'] ?? $json;

        $dumps = [
            'districts' => [],
            'counties' => [],
            'sub_counties' => [],
            'parishes' => [],
            'villages' => [],
        ];

        foreach ($data as $district) {
            $dumps['districts'][] = $this->formatRow(['id' => $district['id'], 'name' => $district['name']]);

            foreach ($district['counties'] ?? [] as $county) {
                $dumps['counties'][] = $this->formatRow(['id' => $county['id'], 'district_id' => $district['id'], 'name' => $county['name']]);

                foreach ($county['sub_counties'] ?? [] as $subCounty) {
                    $dumps['sub_counties'][] = $this->formatRow(['id' => $subCounty['id'], 'county_id' => $county['id'], 'name' => $subCounty['name']]);

                    foreach ($subCounty['parishes'] ?? [] as $parish) {
                        $dumps['parishes'][] = $this->formatRow(['id' => $parish['id'], 'sub_county_id' => $subCounty['id'], 'name' => $parish['name']]);

                        foreach ($parish['villages'] ?? [] as $village) {
                            $dumps['villages'][] = $this->formatRow(['id' => $village['id'], 'parish_id' => $parish['id'], 'name' => $village['name']]);
                        }
                    }
                }
            }
        }

        $dumpDir = __DIR__ . '/../../database/dumps';
        File::ensureDirectoryExists($dumpDir);

        foreach ($dumps as $table => $rows) {
            if ($onProgress) {
                $onProgress("Building dump for table: ug_{$table}");
            }
            $this->writeDumpFile($dumpDir . "/{$table}.sql", "ug_{$table}", $rows);
        }
    }

    protected function formatRow(array $data): array
    {
        if (config('ug-village-locations.use_uuids', false)) {
            $data['uuid'] = (string) \Illuminate\Support\Str::uuid();
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function writeDumpFile(string $path, string $table, array $rows): void
    {
        if (empty($rows)) {
            File::put($path, "");
            return;
        }

        $columns = array_keys($rows[0]);
        $columnsSql = implode('`, `', $columns);

        $chunks = array_chunk($rows, 100);

        $fullSql = "";
        foreach ($chunks as $chunk) {
            $values = [];
            foreach ($chunk as $row) {
                $escapedValues = array_map(function($v) {
                    if (is_null($v)) return 'NULL';
                    if (is_numeric($v)) return $v;
                    return "'" . str_replace("'", "''", $v) . "'";
                }, array_values($row));
                $values[] = "(" . implode(', ', $escapedValues) . ")";
            }
            $fullSql .= "INSERT INTO `{$table}` (`{$columnsSql}`) VALUES " . implode(",\n", $values) . ";\n";
        }

        File::put($path, $fullSql);
    }
}
