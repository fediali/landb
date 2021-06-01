<?php

Route::group(['namespace' => 'Botble\Threadvariationsamples\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'threadvariationsamples', 'as' => 'threadvariationsamples.'], function () {
            Route::resource('', 'ThreadvariationsamplesController')->parameters(['' => 'threadvariationsamples']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ThreadvariationsamplesController@deletes',
                'permission' => 'threadvariationsamples.destroy',
            ]);

            Route::get('sample-media-list/{id}', [
                'as'         => 'sampleMediaList',
                'uses'       => 'ThreadvariationsamplesController@sampleMediaList',
                'permission' => 'threadvariationsamples.index',
            ]);
            Route::post('upload-sample-media/{id}', [
                'as'         => 'uploadSampleMedia',
                'uses'       => 'ThreadvariationsamplesController@uploadSampleMedia',
                'permission' => 'threadvariationsamples.index',
            ]);
        });
    });

});
