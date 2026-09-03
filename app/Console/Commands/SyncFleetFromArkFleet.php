<?php

namespace App\Console\Commands;

use App\Support\FleetCache;
use Illuminate\Console\Command;

class SyncFleetFromArkFleet extends Command
{
    protected $signature = 'fleet:sync';

    protected $description = 'Pull units from ARK Fleet /equipments and upsert fleet_equipment_cache + fleet_model_cache';

    public function handle(FleetCache $cache): int
    {
        $result = $cache->sync();

        if ($result['skipped'] ?? false) {
            $this->warn('Skipped ARK Fleet fetch — FLEET_API_ENABLED=false');
            $this->info("Derived {$result['models_synced']} models from local unit cache.");

            return self::SUCCESS;
        }

        $this->info("Synced {$result['synced']} units, {$result['models_synced']} models from ARK Fleet.");

        return self::SUCCESS;
    }
}
