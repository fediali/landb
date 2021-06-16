<?php

Route::group(['namespace' => 'Botble\Timeline\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'timelines', 'as' => 'timeline.'], function () {
            Route::resource('', 'TimelineController')->parameters(['' => 'timeline']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'TimelineController@deletes',
                'permission' => 'timeline.destroy',
            ]);
        });
    });

});
