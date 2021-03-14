<?php

Route::group(['namespace' => 'Botble\Threadorders\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'threadorders', 'as' => 'threadorders.'], function () {
            Route::resource('', 'ThreadordersController')->parameters(['' => 'threadorders']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ThreadordersController@deletes',
                'permission' => 'threadorders.destroy',
            ]);
        });
    });

});
