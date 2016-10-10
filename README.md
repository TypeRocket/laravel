## TypeRocket for Laravel 5.3

Originally for WordPress, TypeRocket makes building advanced forms and fields easy for Laravel too.

See http://typerocket.com for documentation (mainly for WordPress).

### Installing

```
composer require typerocket/laravel
```

[Laravel Service Providers](https://laravel.com/docs/5.3/providers#registering-providers) make a way for extending Laravel. TypeRocket Laravel 2.0 is a service provider for Laravel. In your `config/app.php` file add:

```php
'providers' => [
    // Other Service Providers

    TypeRocket\Service::class,
],
```

Then form the command line:

```
php artisan vendor:publish --provider="TypeRocket\Service"
```

You can now access the `config/typerocket.php`.

Finally, add uploads to public folder. From your site root directory run:

```
ln -s ../storage/uploads uploads
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
$form = new \TypeRocket\Form('Post', 'update', $id, '/posts/' . $id);
```

```php
<div class="typerocket-container">
    {!! $form->open() !!}
    {!! $form->text('title')->setLabel('Post Title') !!}
    {!! $form->checkbox('publish')->setText('Published') !!}
    {!! $form->close('Submit') !!}
</div>
```

## Request Old Input

To load old input into the form set the request.

```php
class PostController extends Controller
{
    public function create(Request $request)
    {
        $form = new \TypeRocket\Form('Post', 'create', null, '/posts/');
        $form->setRequest($request); // set request
        return view('posts.create', ['form' => $form]);
    }
}
```

## Validate

```php
class PostController extends Controller
{

    public function store(Request $request)
    {
        $tr = $request->input('tr');

        $validator = \Validator::make($tr, [
            'title' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return redirect("posts/create")
                ->withErrors($validator)
                ->withInput();
        }

        $post = new Fabric();
        $post->title = $tr['title'];
        $post->save();

        header('Location: /posts/');
    }

}
```

## Matrix route.

Working with matrix fields the service provider will add this for you.

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

## Matrix Assets

Assets will not be loaded form fields because the view is already loaded. Include the possible assets in the controller.

For example a Matrix field that uses an image field will need to include `image.js`.

```php
$paths = \TypeRocket\Config::getPaths();
\TypeRocket\Assets::addToFooter('js', 'typerocket-image', $paths['urls']['js'] . '/image.js');
```

## Media

Typerocket Media uses https://github.com/eventviva/php-image-resize to create thumbnails.
