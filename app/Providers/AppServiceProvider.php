<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // デバック用の設定
        Model::shouldBeStrict(! app()->environment('production'));
        // Model::shouldBeStrict(! $this->app->isProduction());
    }
}
