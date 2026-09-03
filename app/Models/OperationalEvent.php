<?php

namespace App\Models;

use App\Support\BdAges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalEvent extends Model
{
    protected $fillable = [
        'fleet_equipment_id',
        'status',
        'priority',
        'started_at',
        'ended_at',
        'closed_to',
        'hm_snapshot',
        'hm_corrected',
        'problem',
        'mr_no',
        'pr_no',
        'po_no',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'hm_snapshot' => 'decimal:2',
            'hm_corrected' => 'decimal:2',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(FleetUnit::class, 'fleet_equipment_id', 'fleet_equipment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOpen(): bool
    {
        return $this->ended_at === null;
    }

    public function durationHours(): ?int
    {
        if ($this->ended_at === null) {
            return null;
        }

        return (int) $this->started_at->diffInHours($this->ended_at);
    }

    public function bdAgesHours(): ?int
    {
        if (! $this->isOpen() || $this->status !== 'breakdown') {
            return null;
        }

        return BdAges::hours($this->started_at);
    }

    public function bdAgesTooltip(): ?string
    {
        if (! $this->isOpen() || $this->status !== 'breakdown') {
            return null;
        }

        return BdAges::tooltip($this->started_at);
    }
}
