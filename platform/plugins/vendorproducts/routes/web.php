<?php

Route::group(['namespace' => 'Botble\Vendorproducts\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'vendorproducts', 'as' => 'vendorproducts.'], function () {
            Route::resource('', 'VendorproductsController')->parameters(['' => 'vendorproducts']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'VendorproductsController@deletes',
                'permission' => 'vendorproducts.destroy',
            ]);
        });
    });

});
