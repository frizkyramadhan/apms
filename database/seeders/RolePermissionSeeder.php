<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dmbd.dashboard',
            'dmbd.monitor',
            'dmbd.write',
            'dmbd.master',
            'dmbd.export',
            'users.access',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.access',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.access',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name);
        }

        $roles = [
            'administrator' => $permissions,
            'plant_foreman' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.write', 'dmbd.master', 'dmbd.export'],
            'production_superintendent' => ['dmbd.dashboard', 'dmbd.monitor'],
            'logistics' => [],
            'plant_superintendent' => ['dmbd.dashboard', 'dmbd.monitor'],
            'project_manager' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.export'],
            'plant_manager' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.export'],
            'operational_gm' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.export'],
            'operational_director' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.export'],
            'commercial_treasury_director' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.export'],
            'president_director' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.export'],
        ];

        foreach ($roles as $name => $codes) {
            $role = Role::findOrCreate($name);
            $role->syncPermissions($codes);
        }
    }
}
