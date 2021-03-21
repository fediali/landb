<?php

Route::group(['namespace' => 'Botble\Fabrics\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'fabrics', 'as' => 'fabrics.'], function () {
            Route::resource('', 'FabricsController')->parameters(['' => 'fabrics']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'FabricsController@deletes',
                'permission' => 'fabrics.destroy',
            ]);
        });
    });

});
