<?php
return [
    'vue' => true,
    'form' => \TypeRocket\Form::class,
    'debug' => env('TR_DEBUG', true),
    'seed' => env('TR_SEED', 'replaceThis'),
    'urls' => [
        'js' => env('TR_JS_URL', '/typerocket/js/'),
        'css' => env('TR_CSS_URL', '/typerocket/css/'),
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
        'folder' => $_SERVER['DOCUMENT_ROOT'] . '/../matrix',
        'api_url' => '/matrix_api'
    ]
];
