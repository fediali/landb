<?php

Route::group(['namespace' => 'Botble\Rises\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'rises', 'as' => 'rises.'], function () {
            Route::resource('', 'RisesController')->parameters(['' => 'rises']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'RisesController@deletes',
                'permission' => 'rises.destroy',
            ]);
        });
    });

});
