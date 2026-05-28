<?php

namespace Gp10devhts\UgVillageLocations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class District extends Model
{
    protected $table = 'ug_districts';
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

    public function counties()
    {
        return $this->hasMany(config('ug-village-locations.models.county'));
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }
}
