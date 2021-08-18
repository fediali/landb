<?php

Route::group(['namespace' => 'Botble\Orderstatuses\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'orderstatuses', 'as' => 'orderstatuses.'], function () {
            Route::resource('', 'OrderstatusesController')->parameters(['' => 'orderstatuses']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'OrderstatusesController@deletes',
                'permission' => 'orderstatuses.destroy',
            ]);
        });
    });
});
