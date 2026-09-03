<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FleetModel extends Model
{
    protected $table = 'fleet_model_cache';

    protected $primaryKey = 'fleet_model_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'fleet_model_id',
        'model_name',
        'manufacture',
        'plant_group',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'synced_at' => 'datetime',
        ];
    }

    public function units(): HasMany
    {
        return $this->hasMany(FleetUnit::class, 'fleet_model_id', 'fleet_model_id');
    }
}
