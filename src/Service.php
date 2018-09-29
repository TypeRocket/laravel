<?php

namespace TypeRocket;

use Illuminate\Support\Facades\Blade;
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
        Assets::addToFooter('js', 'typerocket-jquery', $paths['urls']['js'] . '/jquery-3.1.1.min.js');
        // Assets::addToFooter('js', 'typerocket-core', $paths['urls']['js'] . '/typerocket.js');
        Assets::addToHead('js', 'typerocket-global', $paths['urls']['js'] . '/global.js');
        Assets::addToHead('css', 'typerocket-core', $paths['urls']['css'] . '/typerocket.css');

        if (config('typerocket.media.unsplash.enabled')) {
            Assets::addToFooter('js', 'typerocket-unsplash', $paths['urls']['js'] . '/unsplash.js');
        }
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

        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/typerocket'),
        ], 'views');

        Blade::directive('tr_unsplash', function ($expression) {
            if (\config('typerocket.media.unsplash.enabled', false) === false) {
                return '';
            }

            return sprintf(
                '<span id="tr-unsplash" data-client-id="%s" data-upload-url="%s" data-csrf="%s">' .
                '<button type="button" class="btn btn-default">Search Unsplash</button>' .
                '</span>',
                \config('typerocket.media.unsplash.client_id'),
                route('media.index'),
                '<?php echo csrf_token(); ?>'
            );
        });
    }
}
