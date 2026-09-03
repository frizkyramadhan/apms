<?php

namespace App\Providers;

use App\Helpers\Helpers;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        AliasLoader::getInstance()->alias('Helper', Helpers::class);
    }

    public function boot(): void
    {
        //
    }
}
