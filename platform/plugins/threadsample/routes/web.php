<?php

Route::group(['namespace' => 'Botble\Threadsample\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'threadsamples', 'as' => 'threadsample.'], function () {
            Route::resource('', 'ThreadsampleController')->parameters(['' => 'threadsample']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ThreadsampleController@deletes',
                'permission' => 'threadsample.destroy',
            ]);
        });
    });

});
