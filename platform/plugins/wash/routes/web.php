<?php

Route::group(['namespace' => 'Botble\Wash\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'washes', 'as' => 'wash.'], function () {
            Route::resource('', 'WashController')->parameters(['' => 'wash']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'WashController@deletes',
                'permission' => 'wash.destroy',
            ]);
        });
    });

});
