<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserProject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportPcrUsers extends Command
{
    protected $signature = 'pcr:import-users';

    protected $description = 'Import users, roles, and project scope from arka_pcr_new';

    public function handle(): int
    {
        $rows = DB::connection('pcr')->table('user')->get();

        foreach ($rows as $row) {
            $user = User::updateOrCreate(
                ['pcr_user_id' => $row->id_user],
                [
                    'username' => $row->username,
                    'name' => $row->full_name,
                    'email' => $row->email,
                    'password' => $row->password,
                    'is_active' => (bool) $row->is_active,
                ]
            );

            $roleNames = DB::connection('pcr')
                ->table('user_role')
                ->join('role', 'role.id_role', '=', 'user_role.id_role')
                ->where('user_role.id_user', $row->id_user)
                ->pluck('role.name')
                ->all();

            if ($roleNames !== []) {
                $user->syncRoles($roleNames);
            }

            $codes = DB::connection('pcr')
                ->table('user_project')
                ->where('id_user', $row->id_user)
                ->pluck('project_code')
                ->all();

            UserProject::where('user_id', $user->id)->delete();
            foreach ($codes as $code) {
                UserProject::create(['user_id' => $user->id, 'project_code' => $code]);
            }

            $this->line("imported {$user->username}");
        }

        $this->info("Imported {$rows->count()} users.");

        return self::SUCCESS;
    }
}
