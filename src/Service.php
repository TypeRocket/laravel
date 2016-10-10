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
        $paths = Config::getPaths();
        // type ( js || css), id, path
        Assets::addToFooter('js', 'typerocket-core', $paths['urls']['js'] . '/typerocket.js');
        Assets::addToHead('js', 'typerocket-global', $paths['urls']['js'] . '/global.js');
    }

    public function boot()
    {
        if (! $this->app->routesAreCached() ) {
            require __DIR__.'/../routes.php';
        }

        $this->loadMigrationsFrom(__DIR__.'/../migrations');

        $this->publishes([
            __DIR__.'/../config.php' => config_path('typerocket.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../public' => public_path('typerocket'),
        ], 'public');

        $this->loadViewsFrom(__DIR__.'/../views', 'typerocket');
    }
}