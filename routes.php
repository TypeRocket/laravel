<?php
Route::post('matrix_api/{group}/{type}', function($group, $type) {
    (new TypeRocket\Matrix())->route($group, $type);
});
Route::get('media/jfeed', '\TypeRocket\Controllers\TypeRocketMediaController@jfeed');
Route::resource('media', '\TypeRocket\Controllers\TypeRocketMediaController');