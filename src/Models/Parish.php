<?php

namespace Gp10devhts\UgVillageLocations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Parish extends Model
{
    protected $table = 'ug_parishes';
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

    public function subCounty()
    {
        return $this->belongsTo(config('ug-village-locations.models.sub_county'));
    }

    public function villages()
    {
        return $this->hasMany(config('ug-village-locations.models.village'));
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }
}
