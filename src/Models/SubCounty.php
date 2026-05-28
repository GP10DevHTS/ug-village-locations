<?php

namespace Gp10devhts\UgVillageLocations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubCounty extends Model
{
    protected $table = 'ug_sub_counties';
    protected $guarded = [];
    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($model) {
            if (config('ug-village-locations.use_uuids', false) && empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function county()
    {
        return $this->belongsTo(config('ug-village-locations.models.county'));
    }

    public function parishes()
    {
        return $this->hasMany(config('ug-village-locations.models.parish'));
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }
}
