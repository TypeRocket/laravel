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

## Forms

```php
// model, action ( create || update ), id, path
$form = new Form('catalog', 'create', $id, '/catalogs/process/' . $id);
```

```php
<div class="typerocket-container">
    {!! $form->open() !!}
    {!! $form->select('Converter')->setOptions($converters) !!}
    {!! $form->checkbox('Run')->setText('Execute and run the converter.') !!}
    {!! $form->checkbox('Append')->setText('Append to existing table data.') !!}
    {!! $form->close('Submit') !!}
</div>
```
