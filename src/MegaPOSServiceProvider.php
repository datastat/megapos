<?php

namespace Datastat\MegaPOS;

use Illuminate\Support\ServiceProvider;

class MegaPOSServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {

        if (! $this->app->routesAreCached()) {
            require __DIR__.'/routes.php';
        }

        $this->loadViewsFrom(__DIR__.'/views', 'megapos');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/megapos'),
            __DIR__.'/config/config.php' => config_path('megapos.php'),
        ]);

    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind('megapos', function ($app) {

            return new MegaPOSService($app['validator']);

        });

        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'megapos');

    }
}