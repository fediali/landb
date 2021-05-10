<?php

Route::group(['namespace' => 'Botble\Vendororderstatuses\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'vendororderstatuses', 'as' => 'vendororderstatuses.'], function () {
            Route::resource('', 'VendororderstatusesController')->parameters(['' => 'vendororderstatuses']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'VendororderstatusesController@deletes',
                'permission' => 'vendororderstatuses.destroy',
            ]);
        });
    });

});
