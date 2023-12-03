<?php

namespace GlennRaya\Xendivel;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class XendivelServiceProvider extends ServiceProvider
{
    /**
     * Register Xendivel services.
     */
    public function register(): void
    {
        $this->app->singleton(XenditApi::class, function () {
            return new XenditApi();
        });
    }

    /**
     * Bootstrap Xendivel services.
     */
    public function boot(): void
    {
        // Publishes all assets of Xendivel like config file, invoice templates, checkout views, etc.
        // `php artisan vendor:publish --tag=xendivel`
        $this->publishes([
            __DIR__.'/../config/xendivel.php' => config_path('xendivel.php'),
            __DIR__.'/../resources/views/' => resource_path('views/vendor/xendivel/views/'),
        ], 'xendivel');

        // Xendivel ships with example web and API routes so you could easily test
        // the example files like the cards, ewallet, and subscription templates.
        $this->loadRoutesFrom(__DIR__.'/../routes/xendivel-routes.php');

        // Response macro to delete the invoice from storage after the download is complete.
        // This would save storage space on the host server.
        Response::macro('downloadAndDelete', function ($path, $name = null, $headers = []) {
            return response()->download($path, $name, $headers)->deleteFileAfterSend(true);
        });
    }
}
