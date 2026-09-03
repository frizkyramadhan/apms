<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function fleetModel(): BelongsTo
    {
        return $this->belongsTo(FleetModel::class, 'fleet_model_id', 'fleet_model_id');
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

    /** ARK Fleet unitStatus filter (ACTIVE / IN-ACTIVE / SCRAP / SOLD), same matching as arka-pcr. */
    public function scopeMatchingFleetStatus($query, ?string $status)
    {
        if ($status === null || $status === '') {
            return $query;
        }

        $compact = "UPPER(REPLACE(REPLACE(COALESCE(unit_status,''), ' ', ''), '-', ''))";
        $needle = strtoupper(str_replace([' ', '-'], '', $status));

        if ($needle === 'ACTIVE') {
            return $query->whereRaw("{$compact} = 'ACTIVE'");
        }

        if ($needle === 'INACTIVE') {
            return $query->whereRaw("{$compact} = 'INACTIVE'");
        }

        return $query->whereRaw("{$compact} = ?", [$needle]);
    }
}
