<?php

Route::group(['namespace' => 'Botble\Timeline\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'timelines', 'as' => 'timeline.'], function () {
            Route::resource('', 'TimelineController')->parameters(['' => 'timeline']);
            Route::post('store', [
                'as'         => 'store',
                'uses'       => 'TimelineController@store',
                'permission' => 'timeline.create',
            ]);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'TimelineController@deletes',
                'permission' => 'timeline.destroy',
            ]);
        });
    });

});
