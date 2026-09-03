<?php

$base = rtrim((string) env('FLEET_API_URL', 'http://192.168.32.15/ark-fleet/api'), '/');

return [
    /*
    | Empty / unset FLEET_API_ENABLED means enabled (same as PCR Next).
    | Set false / 0 / no / off to develop without ark-fleet connectivity.
    */
    'enabled' => env('FLEET_API_ENABLED'),

    'timeout_ms' => (int) env('FLEET_API_TIMEOUT_MS', 15_000),

    'base_url' => $base,

    'projects_url' => env('PROJECTS_API_URL') ?: $base.'/projects',

    'units_url' => env('ARK_FLEET_UNITS_URL') ?: $base.'/equipments',
];
