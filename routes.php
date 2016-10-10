<?php
Route::group(['middleware' => config('typerocket.matrix.middleware')], function () {
    Route::post('matrix_api/{group}/{type}', function ($group, $type) {
        (new TypeRocket\Matrix())->route($group, $type);
    });
});

Route::group(['middleware' => config('typerocket.media.middleware')], function () {
    Route::get('media/jfeed', '\TypeRocket\Controllers\TypeRocketMediaController@jfeed');
    Route::get('typerocket_media', '\TypeRocket\Controllers\TypeRocketMediaController@jfeed');
    Route::resource('media', '\TypeRocket\Controllers\TypeRocketMediaController');
});
