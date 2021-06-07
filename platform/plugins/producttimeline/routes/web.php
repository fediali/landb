<?php

Route::group(['namespace' => 'Botble\Producttimeline\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'producttimelines', 'as' => 'producttimeline.'], function () {
            Route::resource('', 'ProducttimelineController')->parameters(['' => 'producttimeline']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ProducttimelineController@deletes',
                'permission' => 'producttimeline.destroy',
            ]);
        });
    });

});
