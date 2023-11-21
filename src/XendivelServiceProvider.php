<?php

namespace GlennRaya\Xendivel;

use Illuminate\Support\ServiceProvider;

class XendivelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Xendivel::class, function () {
            return new Xendivel();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
