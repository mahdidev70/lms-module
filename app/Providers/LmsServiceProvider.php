<?php

namespace TechStudio\Lms\app\Providers;

use Illuminate\Support\ServiceProvider;

class LmsServiceProvider extends ServiceProvider
{

    public function boot()
    {
        // $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
