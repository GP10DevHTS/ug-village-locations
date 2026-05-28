<?php

namespace Gp10devhts\UgVillageLocations;

class UgLocations
{
    public function districtModel(): string
    {
        return config('ug-village-locations.models.district');
    }

    public function countyModel(): string
    {
        return config('ug-village-locations.models.county');
    }

    public function subCountyModel(): string
    {
        return config('ug-village-locations.models.sub_county');
    }

    public function parishModel(): string
    {
        return config('ug-village-locations.models.parish');
    }

    public function villageModel(): string
    {
        return config('ug-village-locations.models.village');
    }

    public function districts()
    {
        return $this->districtModel()::all();
    }

    public function counties(?int $districtId = null)
    {
        $query = $this->countyModel()::query();
        if ($districtId) {
            $query->where('district_id', $districtId);
        }
        return $query->get();
    }

    public function subCounties(?int $countyId = null)
    {
        $query = $this->subCountyModel()::query();
        if ($countyId) {
            $query->where('county_id', $countyId);
        }
        return $query->get();
    }

    public function parishes(?int $subCountyId = null)
    {
        $query = $this->parishModel()::query();
        if ($subCountyId) {
            $query->where('sub_county_id', $subCountyId);
        }
        return $query->get();
    }

    public function villages(?int $parishId = null)
    {
        $query = $this->villageModel()::query();
        if ($parishId) {
            $query->where('parish_id', $parishId);
        }
        return $query->get();
    }
}
