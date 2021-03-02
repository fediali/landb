<?php

Route::group(['namespace' => 'Botble\Packs\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'packs', 'as' => 'packs.'], function () {
            Route::resource('', 'PacksController')->parameters(['' => 'packs']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'PacksController@deletes',
                'permission' => 'packs.destroy',
            ]);
        });
    });

});
