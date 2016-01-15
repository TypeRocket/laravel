## TypeRocket for Laravel 5.1

Originally for WordPress, TypeRocket makes building advanced forms and fields easy for Laravel too.

See http://typerocket.com for documentation (mainly for WordPress).

### .env

Example settings.

```
TR_MATRIX_API_URL=/matrix_api
TR_JS_URL=/js/tr
TR_CSS_URL=/css/tr
TR_DEBUG=true
TR_DOMAIN=App
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
