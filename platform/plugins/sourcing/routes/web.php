<?php

Route::group(['namespace' => 'Botble\Sourcing\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'sourcings', 'as' => 'sourcing.'], function () {
            Route::resource('', 'SourcingController')->parameters(['' => 'sourcing']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'SourcingController@deletes',
                'permission' => 'sourcing.destroy',
            ]);
        });
    });

});
