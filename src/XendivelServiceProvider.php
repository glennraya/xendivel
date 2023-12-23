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
        // Xendivel's configuration file.
        $this->mergeConfigFrom(
            __DIR__.'/../config/xendivel.php', 'xendivel'
        );

        // Built-in routes for Xendivel
        $this->loadRoutesFrom(__DIR__.'/../routes/xendivel-routes.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'xendivel');

        // Publishes Xendivel's config file.
        // `php artisan vendor:publish --tag=xendivel-config`
        $this->publishes([
            __DIR__.'/../config/xendivel.php' => config_path('xendivel.php'),
        ], 'xendivel-config');

        // Publishes Xendivel's example images and css.
        // `php artisan vendor:publish --tag=xendivel-public`
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/xendivel'),
        ], 'xendivel-public');

        // Publishes Xendivel's view files like the checkout template, invoice template and css.
        // `php artisan vendor:publish --tag=xendivel-assets`
        $this->publishes([
            __DIR__.'/../resources/views/' => resource_path('views/vendor/xendivel/views/'),
        ], 'xendivel-views');

        // Publishes all Xendivel's files like config, views, and public assets all at once.
        $this->publishes([
            __DIR__.'/../config/xendivel.php' => config_path('xendivel.php'),
            __DIR__.'/../resources/views/' => resource_path('views/vendor/xendivel/views/'),
            __DIR__.'/../public' => public_path('vendor/xendivel'),
        ], 'xendivel');

        // Publishes Xendivel's webhook listener.
        $this->publishes([
            __DIR__.'/Events/eWalletEvents.php' => app_path('Events/eWalletEvents.php'),
            __DIR__.'/Listeners/eWalletWebhookListener.php' => app_path('Listeners/eWalletWebhookListener.php'),
        ], 'xendivel-webhook-listener');

        // Response macro to delete the invoice from storage after the download is complete.
        // This would save storage space on the host server.
        Response::macro('downloadAndDelete', function ($path, $name = null, $headers = []) {
            return response()->download($path, $name, $headers)->deleteFileAfterSend(true);
        });

        // Register the webhook verification middleware
        $this->app['router']->aliasMiddleware('xendit-webhook-verification', \GlennRaya\Xendivel\Http\Middleware\VerifyWebhookSignature::class);
    }
}
