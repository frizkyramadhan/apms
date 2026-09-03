<?php

namespace App\Support;

use App\Models\FleetModel;
use App\Models\FleetUnit;
use App\Models\User;
use Illuminate\Support\Collection;

class FleetCache
{
    public function __construct(private FleetApi $api) {}

    /** Map ARK Fleet unit payload → fleet_equipment_cache columns. */
    public static function unitToCacheFields(array $unit): array
    {
        return [
            'fleet_equipment_id' => (int) $unit['id'],
            'unit_no' => (string) $unit['unit_no'],
            'description' => $unit['description'] ?? null,
            'project_code' => (string) $unit['project_code'],
            'fleet_model_id' => (int) $unit['model_id'],
            'model_name' => ($unit['model'] ?? '') ?: null,
            'manufacture' => ($unit['manufacture'] ?? '') ?: null,
            'plant_group' => ($unit['plant_group'] ?? '') ?: null,
            'plant_type' => ($unit['plant_type'] ?? '') ?: null,
            'unit_status' => $unit['unitstatus'] ?? null,
            'serial_number' => ($unit['serial_no'] ?? '') ?: null,
            'engine_number' => ($unit['machine_no'] ?? '') ?: null,
            'synced_at' => now(),
        ];
    }

    public static function modelFromEquipment(array $eq): array
    {
        return [
            'fleet_model_id' => (int) $eq['model_id'],
            'model_name' => ($eq['model'] ?? '') ?: null,
            'manufacture' => ($eq['manufacture'] ?? '') ?: null,
            'plant_group' => ($eq['plant_group'] ?? '') ?: null,
        ];
    }

    /**
     * Pull /equipments from ARK Fleet and upsert local cache.
     * Models first (same order as PCR). Units with model_id <= 0 are skipped.
     *
     * @return array{synced: int, models_synced: int, skipped?: bool}
     */
    public function sync(): array
    {
        if (! $this->api->enabled()) {
            return [
                'synced' => 0,
                'models_synced' => $this->syncModelsFromUnitCache(),
                'skipped' => true,
            ];
        }

        $equipments = $this->api->equipments();
        $modelsById = [];

        foreach ($equipments as $eq) {
            $eq = (array) $eq;
            $modelId = (int) ($eq['model_id'] ?? 0);
            if ($modelId > 0 && ! isset($modelsById[$modelId])) {
                $modelsById[$modelId] = self::modelFromEquipment($eq);
            }
        }

        $syncedAt = now();
        foreach ($modelsById as $model) {
            FleetModel::updateOrCreate(
                ['fleet_model_id' => $model['fleet_model_id']],
                $model + ['synced_at' => $syncedAt]
            );
        }

        $units = 0;
        foreach ($equipments as $eq) {
            $eq = (array) $eq;
            if ((int) ($eq['model_id'] ?? 0) <= 0) {
                continue;
            }

            $fields = self::unitToCacheFields($eq);
            $id = $fields['fleet_equipment_id'];
            unset($fields['fleet_equipment_id']);

            FleetUnit::updateOrCreate(['fleet_equipment_id' => $id], $fields);
            $units++;
        }

        return ['synced' => $units, 'models_synced' => count($modelsById)];
    }

    /**
     * Live PROJECTS_API_URL, then distinct project_code from unit cache.
     *
     * @return array{items: list<array{project_code: string, bowheer: string, location: string}>, source: 'projects-api'|'cache'}
     */
    public function projectsForUserAdmin(): array
    {
        if ($this->api->enabled()) {
            try {
                return ['items' => $this->normalizeProjects($this->api->projects()), 'source' => 'projects-api'];
            } catch (\Throwable) {
                return ['items' => $this->cachedProjects(), 'source' => 'cache'];
            }
        }

        return ['items' => $this->cachedProjects(), 'source' => 'cache'];
    }

    /** User-management dropdown: live/cache project codes plus sentinel 000H. */
    public function codesForUserForm(): Collection
    {
        $codes = collect($this->projectsForUserAdmin()['items'])
            ->pluck('project_code')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if (! $codes->contains('000H')) {
            $codes = $codes->prepend('000H');
        }

        return $codes;
    }

    /** Site filter dropdown, scoped to the signed-in user's projects. */
    public function codesForFilter(User $user): Collection
    {
        $codes = collect($this->projectsForUserAdmin()['items'])
            ->pluck('project_code')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if (! $user->seesAllSites()) {
            $codes = $codes->intersect($user->projectCodes())->values();
        }

        return $codes;
    }

    private function syncModelsFromUnitCache(): int
    {
        $rows = FleetUnit::query()
            ->where('fleet_model_id', '>', 0)
            ->get(['fleet_model_id', 'model_name', 'manufacture', 'plant_group'])
            ->unique('fleet_model_id');

        $syncedAt = now();
        foreach ($rows as $row) {
            FleetModel::updateOrCreate(
                ['fleet_model_id' => $row->fleet_model_id],
                [
                    'model_name' => $row->model_name,
                    'manufacture' => $row->manufacture,
                    'plant_group' => $row->plant_group,
                    'synced_at' => $syncedAt,
                ]
            );
        }

        return $rows->count();
    }

    /** @return list<array{project_code: string, bowheer: string, location: string}> */
    private function cachedProjects(): array
    {
        return FleetUnit::query()
            ->select('project_code')
            ->distinct()
            ->orderBy('project_code')
            ->pluck('project_code')
            ->map(fn (string $code) => [
                'project_code' => $code,
                'bowheer' => $code,
                'location' => '',
            ])
            ->values()
            ->all();
    }

    /** @return list<array{project_code: string, bowheer: string, location: string}> */
    private function normalizeProjects(array $items): array
    {
        $out = [];
        foreach ($items as $item) {
            $item = (array) $item;
            $code = trim((string) ($item['project_code'] ?? ''));
            if ($code === '') {
                continue;
            }
            $out[] = [
                'project_code' => $code,
                'bowheer' => (string) ($item['bowheer'] ?? $code),
                'location' => (string) ($item['location'] ?? ''),
            ];
        }

        return $out;
    }
}
