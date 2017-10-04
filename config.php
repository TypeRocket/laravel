<?php
return [
    'vue' => true,
    'form' => \TypeRocket\Form::class,
    'db_table' => 'tr_media',
    'view' => [
        'extends' => 'layouts.app',
        'section' => 'content',
    ],
    'debug' => env('TR_DEBUG', true),
    'seed' => env('TR_SEED', 'replaceThis'),
    'urls' => [
        'js' => env('TR_JS_URL', '/typerocket/js/'),
        'css' => env('TR_CSS_URL', '/typerocket/css/'),
        'components' => env('TR_COMPONENT_URL', '/typerocket/components/'),
    ],
    'media' => [
        'middleware' => ['web', 'auth'],
        'controller_middleware' => [
            'destroy' => []
        ],
        'uploads' => '/uploads/media/',
        'processors' => [
            \TypeRocket\MediaProcesses\Setup::class,
            \TypeRocket\MediaProcesses\LocalStorage::class
        ]
    ],
    'matrix' => [
        'middleware' => ['web', 'auth'],
        'folder' =>  base_path('/matrix'),
        'api_url' => '/matrix_api'
    ],
    'route' => [
        'controller' => \TypeRocket\Controllers\TypeRocketMediaController::class,
        'jfeed' => \TypeRocket\Controllers\TypeRocketMediaController::class . '@jfeed',
    ]
];
