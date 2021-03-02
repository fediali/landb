<?php

Route::group(['namespace' => 'Botble\Printdesigns\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'printdesigns', 'as' => 'printdesigns.'], function () {
            Route::resource('', 'PrintdesignsController')->parameters(['' => 'printdesigns']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'PrintdesignsController@deletes',
                'permission' => 'printdesigns.destroy',
            ]);
        });
    });

});
