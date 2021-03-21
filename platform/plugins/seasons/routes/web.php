<?php

Route::group(['namespace' => 'Botble\Seasons\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'seasons', 'as' => 'seasons.'], function () {
            Route::resource('', 'SeasonsController')->parameters(['' => 'seasons']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'SeasonsController@deletes',
                'permission' => 'seasons.destroy',
            ]);
        });
    });

});
