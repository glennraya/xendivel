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
        // Publishes the Xendivel configuration file to the config directory.
        // `php artisan vendor:publish --tag=xendivel-config`
        $this->publishes([
            __DIR__ . '/../config/xendivel.php' => config_path('xendivel.php'),
        ], 'xendivel-config');
    }
}
