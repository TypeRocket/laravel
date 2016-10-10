<?php
return [
    'vue' => env('TR_VUE', true),
    'form' => env('TR_FORM_PROVIDER', \TypeRocket\Form::class),
    'debug' => env('TR_DEBUG', true),
    'seed' => env('TR_SEED', 'replaceThis'),
    'matrix_folder' => env('TR_MATRIX_FOLDER_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../matrix'),
    'matrix_api' => env('TR_MATRIX_API_URL', '/matrix_api'),
    'js' => env('TR_JS_URL', '/js/tr'),
    'css' => env('TR_CSS_URL', '/css/tr')
];