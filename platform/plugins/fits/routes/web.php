<?php

Route::group(['namespace' => 'Botble\Fits\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'fits', 'as' => 'fits.'], function () {
            Route::resource('', 'FitsController')->parameters(['' => 'fits']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'FitsController@deletes',
                'permission' => 'fits.destroy',
            ]);
        });
    });

});
