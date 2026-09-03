<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserProject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ImportPcrUsers extends Command
{
    protected $signature = 'pcr:import-users {--dir= : TSV dump directory (default storage/app/pcr-import)}';

    protected $description = 'Import PCR permissions, roles, and users (password set to Laravel-hashed Password)';

    public function handle(): int
    {
        $dir = $this->option('dir') ?: storage_path('app/pcr-import');
        foreach (['permission.tsv', 'role.tsv', 'role_permission.tsv', 'user.tsv', 'user_role.tsv', 'user_project.tsv'] as $file) {
            if (! is_file($dir.DIRECTORY_SEPARATOR.$file)) {
                $this->error("Missing {$dir}/{$file}");

                return self::FAILURE;
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = $this->tsv($dir.'/permission.tsv');
        foreach ($permissions as $row) {
            Permission::findOrCreate($row[1]);
        }
        foreach ($this->apmsExtraPermissionNames() as $name) {
            Permission::findOrCreate($name);
        }
        $this->info('Permissions: '.$permissions->count().' from PCR (+ APMS extras).');

        $rolePerms = $this->tsv($dir.'/role_permission.tsv')->groupBy(fn ($row) => $row[0]);
        $roles = $this->tsv($dir.'/role.tsv');
        foreach ($roles as $row) {
            $name = $row[1];
            $role = Role::findOrCreate($name);
            $pcrCodes = $rolePerms->get($name, collect())->map(fn ($r) => $r[1])->all();
            $role->syncPermissions(array_values(array_unique(array_merge($pcrCodes, $this->apmsExtras()[$name] ?? []))));
        }
        $this->info('Roles: '.$roles->count().' from PCR.');

        $password = Hash::make('Password');
        $users = $this->tsv($dir.'/user.tsv');
        $idMap = [];
        foreach ($users as $row) {
            $pcrId = (int) $row[0];
            $email = $row[2] !== '' ? $row[2] : null;
            if ($email && User::query()->where('email', $email)->where('pcr_user_id', '!=', $pcrId)->exists()) {
                $email = null;
            }

            $user = User::updateOrCreate(
                ['pcr_user_id' => $pcrId],
                [
                    'username' => $row[1],
                    'name' => $row[3] !== '' ? $row[3] : $row[1],
                    'email' => $email,
                    'password' => $password,
                    'is_active' => $row[4] === '1',
                    'last_login' => $row[5] !== '' ? $row[5] : null,
                ]
            );
            $idMap[$pcrId] = $user->id;
            $this->line("imported {$user->username}");
        }

        $rolesByUser = $this->tsv($dir.'/user_role.tsv')->groupBy(fn ($row) => $row[0]);
        foreach ($idMap as $pcrId => $userId) {
            $user = User::find($userId);
            $names = $rolesByUser->get((string) $pcrId, collect())->map(fn ($r) => $r[1])->all();
            $user->syncRoles($names);
        }

        UserProject::query()->whereIn('user_id', array_values($idMap))->delete();
        foreach ($this->tsv($dir.'/user_project.tsv') as $row) {
            $userId = $idMap[(int) $row[0]] ?? null;
            if ($userId) {
                UserProject::create(['user_id' => $userId, 'project_code' => $row[1]]);
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info('Imported '.$users->count().' users (password = hashed "Password").');

        return self::SUCCESS;
    }

    private function tsv(string $path): \Illuminate\Support\Collection
    {
        $fh = fopen($path, 'r');
        fgetcsv($fh, 0, "\t");
        $rows = [];
        while (($row = fgetcsv($fh, 0, "\t")) !== false) {
            $rows[] = $row;
        }
        fclose($fh);

        return collect($rows);
    }

    private function apmsExtraPermissionNames(): array
    {
        return array_values(array_unique(array_merge(...array_values($this->apmsExtras()))));
    }

    /** DMBD + APMS user-management perms kept on top of the PCR matrix. */
    private function apmsExtras(): array
    {
        $manage = [
            'dmbd.dashboard', 'dmbd.monitor', 'dmbd.write', 'dmbd.master', 'dmbd.export',
            'users.access', 'users.create', 'users.edit', 'users.delete',
            'roles.access', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.access', 'permissions.create', 'permissions.edit', 'permissions.delete',
        ];

        return [
            'administrator' => $manage,
            'plant_foreman' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.write', 'dmbd.master', 'dmbd.export'],
            'production_superintendent' => ['dmbd.dashboard', 'dmbd.monitor'],
            'plant_superintendent' => ['dmbd.dashboard', 'dmbd.monitor'],
            'project_manager' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.export'],
            'plant_manager' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.export'],
            'operational_gm' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.export'],
            'operational_director' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.export'],
            'commercial_treasury_director' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.export'],
            'president_director' => ['dmbd.dashboard', 'dmbd.monitor', 'dmbd.export'],
        ];
    }
}
