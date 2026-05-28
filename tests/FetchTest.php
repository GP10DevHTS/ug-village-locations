<?php

use Gp10devhts\UgVillageLocations\Providers\LocationProviderInterface;
use Gp10devhts\UgVillageLocations\Services\FetchLocationsService;
use Illuminate\Support\Facades\File;

class MockProvider implements LocationProviderInterface
{
    public function getDistricts(): array
    {
        return [
            ['id' => 1, 'name' => 'APAC'],
            ['id' => 2, 'name' => 'ARUA'],
        ];
    }

    public function getCounties(int $districtId): array
    {
        return [['id' => $districtId * 10, 'name' => 'COUNTY '.$districtId]];
    }

    public function getSubCounties(int $countyId): array
    {
        return [['id' => $countyId * 10, 'name' => 'SUBCOUNTY '.$countyId]];
    }

    public function getParishes(int $subCountyId): array
    {
        return [['id' => $subCountyId * 10, 'name' => 'PARISH '.$subCountyId]];
    }

    public function getVillages(int $parishId): array
    {
        return [['id' => $parishId * 10, 'name' => 'VILLAGE '.$parishId]];
    }
}

beforeEach(function () {
    File::deleteDirectory(__DIR__.'/../resources/data');
});

it('fetches and merges data incrementally', function () {
    $service = new FetchLocationsService(new MockProvider);

    // Fetch all
    $service->fetchAll();

    $rawDir = __DIR__.'/../resources/data/raw';
    expect(File::exists($rawDir.'/001-apac.json'))->toBeTrue();
    expect(File::exists($rawDir.'/002-arua.json'))->toBeTrue();

    $finalPath = __DIR__.'/../resources/data/uganda_locations.json';
    expect(File::exists($finalPath))->toBeTrue();

    $finalData = json_decode(File::get($finalPath), true);
    expect($finalData)->toHaveKey('generated_at');
    expect($finalData['districts_completed'])->toBe(2);
    expect(count($finalData['data']))->toBe(2);

    // Simulate partial fetch and resume
    File::delete($finalPath);
    File::delete($rawDir.'/002-arua.json'); // Force refetch of district 2

    $events = [];
    $service->fetchAll(false, function ($event, $data) use (&$events) {
        $events[] = ['event' => $event, 'data' => $data];
    });

    $skipped = array_filter($events, fn ($e) => $e['event'] === 'district_skipped');
    $started = array_filter($events, fn ($e) => $e['event'] === 'district_start');

    expect(count($skipped))->toBe(1);
    expect($skipped[array_key_first($skipped)]['data']['name'])->toBe('APAC');

    expect(count($started))->toBe(1);
    expect($started[array_key_first($started)]['data']['name'])->toBe('ARUA');

    expect(File::exists($finalPath))->toBeTrue();
});
