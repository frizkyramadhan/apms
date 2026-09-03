<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'pcr_user_id',
        'username',
        'name',
        'email',
        'password',
        'is_active',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login' => 'datetime',
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(UserProject::class);
    }

    public function projectCodes(): array
    {
        return $this->projects()->pluck('project_code')->all();
    }

    public function seesAllSites(): bool
    {
        return in_array('000H', $this->projectCodes(), true)
            || $this->hasRole('administrator');
    }

    public function syncProjectCodes(array $codes): void
    {
        $this->projects()->delete();
        foreach (array_values(array_unique(array_filter($codes))) as $code) {
            $this->projects()->create(['project_code' => (string) $code]);
        }
    }
}
