<?php

Route::group(['namespace' => 'Botble\Vendororder\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'vendororders', 'as' => 'vendororder.'], function () {
            Route::resource('', 'VendororderController')->parameters(['' => 'vendororder']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'VendororderController@deletes',
                'permission' => 'vendororder.destroy',
            ]);
        });
    });

});
