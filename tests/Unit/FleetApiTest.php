<?php

namespace Tests\Unit;

use App\Support\FleetApi;
use App\Support\FleetCache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FleetApiTest extends TestCase
{
    public function test_maps_fleet_unit_payload_to_cache_columns(): void
    {
        $fields = FleetCache::unitToCacheFields([
            'id' => 42,
            'unit_no' => 'EX-01',
            'description' => 'Excavator',
            'project_code' => '017C',
            'model_id' => 9,
            'model' => 'PC200',
            'manufacture' => 'Komatsu',
            'plant_group' => 'Excavator',
            'plant_type' => 'EX',
            'unitstatus' => 'ACTIVE',
            'serial_no' => 'SN1',
            'machine_no' => 'EN1',
        ]);

        $this->assertSame(42, $fields['fleet_equipment_id']);
        $this->assertSame('EX-01', $fields['unit_no']);
        $this->assertSame('017C', $fields['project_code']);
        $this->assertSame(9, $fields['fleet_model_id']);
        $this->assertSame('PC200', $fields['model_name']);
        $this->assertSame('ACTIVE', $fields['unit_status']);
        $this->assertSame('SN1', $fields['serial_number']);
        $this->assertSame('EN1', $fields['engine_number']);
    }

    public function test_unwraps_projects_data_wrapper(): void
    {
        Http::fake([
            config('fleet.projects_url') => Http::response(
                ['data' => [['project_code' => '017C', 'bowheer' => 'Ark', 'location' => 'Site']]],
                200,
                ['Content-Type' => 'application/json']
            ),
        ]);

        $projects = app(FleetApi::class)->projects();

        $this->assertSame('017C', $projects[0]['project_code']);
    }

    public function test_user_form_codes_prefer_live_projects_and_include_000H(): void
    {
        Http::fake([
            config('fleet.projects_url') => Http::response(
                [['project_code' => '022C', 'bowheer' => 'B', 'location' => 'L']],
                200,
                ['Content-Type' => 'application/json']
            ),
        ]);

        $codes = app(FleetCache::class)->codesForUserForm();

        $this->assertTrue($codes->contains('000H'));
        $this->assertTrue($codes->contains('022C'));
    }

    public function test_disabled_flag_skips_http(): void
    {
        config(['fleet.enabled' => 'false']);
        Http::preventStrayRequests();

        $this->expectExceptionMessage('Fleet API is disabled');
        app(FleetApi::class)->projects();
    }
}
