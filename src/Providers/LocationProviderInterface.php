<?php

namespace Gp10devhts\UgVillageLocations\Providers;

interface LocationProviderInterface
{
    public function getDistricts(): array;

    public function getCounties(int $districtId): array;

    public function getSubCounties(int $countyId): array;

    public function getParishes(int $subCountyId): array;

    public function getVillages(int $parishId): array;
}
