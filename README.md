## TypeRocket for Laravel 5.1

Beta is in progress.

### .env

Example settings.

```
TR_MATRIX_API_URL=/matrix_api
TR_JS_URL=/js/tr
TR_CSS_URL=/css/tr
TR_DEBUG=true
```

## Matrix route.

Working with matrix fields.

```php
Route::post('matrix_api/{group}/{type}', function($group, $type) {
    (new TypeRocket\Matrix())->route($group, $type);
});
```

### CSFR for matrix

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'matrix_api/*' // added
    ];
}
```

## JS and CSS init

In blade templates such master templates.

```
{!! \TypeRocket\Assets::getHeadString() !!}
{!! \TypeRocket\Assets::getFooterString() !!}
```

### Adding assets

```php
$paths = Config::getPaths();

// type ( js || css), id, path
Assets::addToFooter('js', 'typerocket-core', $paths['urls']['js'] . '/typerocket.js');
Assets::addToHead('js', 'typerocket-global', $paths['urls']['js'] . '/global.js');
```
