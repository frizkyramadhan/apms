<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FleetUnit extends Model
{
    protected $table = 'fleet_equipment_cache';

    protected $primaryKey = 'fleet_equipment_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'fleet_equipment_id',
        'unit_no',
        'description',
        'project_code',
        'fleet_model_id',
        'model_name',
        'manufacture',
        'plant_group',
        'plant_type',
        'unit_status',
        'serial_number',
        'engine_number',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'synced_at' => 'datetime',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(OperationalEvent::class, 'fleet_equipment_id', 'fleet_equipment_id');
    }

    public function openEvent(): HasOne
    {
        return $this->hasOne(OperationalEvent::class, 'fleet_equipment_id', 'fleet_equipment_id')
            ->whereNull('ended_at')
            ->latestOfMany('started_at');
    }

    public function operationalStatus(): string
    {
        $open = $this->openEvent;

        return $open?->status ?? 'ready';
    }
}
