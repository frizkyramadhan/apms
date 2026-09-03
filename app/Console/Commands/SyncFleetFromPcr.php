<?php

namespace App\Console\Commands;

use App\Models\FleetUnit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncFleetFromPcr extends Command
{
    protected $signature = 'pcr:sync-fleet';

    protected $description = 'Copy fleet_equipment_cache from arka_pcr_new into apms';

    public function handle(): int
    {
        $rows = DB::connection('pcr')->table('fleet_equipment_cache')->get();

        foreach ($rows as $row) {
            FleetUnit::updateOrCreate(
                ['fleet_equipment_id' => $row->fleet_equipment_id],
                [
                    'unit_no' => $row->unit_no,
                    'description' => $row->description,
                    'project_code' => $row->project_code,
                    'fleet_model_id' => $row->fleet_model_id,
                    'model_name' => $row->model_name,
                    'manufacture' => $row->manufacture,
                    'plant_group' => $row->plant_group,
                    'plant_type' => $row->plant_type,
                    'unit_status' => $row->unit_status,
                    'synced_at' => $row->synced_at,
                ]
            );
        }

        $this->info("Synced {$rows->count()} units.");

        return self::SUCCESS;
    }
}
