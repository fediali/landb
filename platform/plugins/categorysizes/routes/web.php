<?php

Route::group(['namespace' => 'Botble\Categorysizes\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'categorysizes', 'as' => 'categorysizes.'], function () {
            Route::resource('', 'CategorysizesController')->parameters(['' => 'categorysizes']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'CategorysizesController@deletes',
                'permission' => 'categorysizes.destroy',
            ]);
        });
    });

});
