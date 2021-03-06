<?php
Route::group(['middleware' => config('typerocket.matrix.middleware') ], function () {
    Route::post('matrix_api/{group}/{type}', function ($group, $type) {
        (new TypeRocket\Matrix())->route($group, $type);
    });
    Route::post('tr_builder_api/v1/{group}/{type}/{folder}', function ($group, $type, $folder) {
        (new TypeRocket\Builder())->route($group, $type, $folder);
    });
});

Route::group(['middleware' => config('typerocket.media.middleware')], function () {
    Route::get('media/jfeed', config('typerocket.route.jfeed', '\TypeRocket\Controllers\TypeRocketMediaController@jfeed'));
    Route::get('typerocket_media', config('typerocket.route.jfeed', '\TypeRocket\Controllers\TypeRocketMediaController@jfeed') );
    Route::resource('media', config('typerocket.route.controller', '\TypeRocket\Controllers\TypeRocketMediaController') );
});
