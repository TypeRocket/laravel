<?php

namespace TypeRocket;

use Illuminate\Support\ServiceProvider;

class Service extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

    public function boot()
    {
        if (! $this->app->routesAreCached() ) {
            require __DIR__.'/../routes.php';
        }

        $this->publishes([
            __DIR__.'/../config.php' => config_path('typerocket.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/assets' => public_path('typerocket'),
        ], 'public');

        $this->loadViewsFrom(__DIR__.'/../views', 'typerocket');
    }
}