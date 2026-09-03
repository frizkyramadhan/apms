<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProject extends Model
{
    protected $table = 'user_project';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['user_id', 'project_code'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
