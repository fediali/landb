<?php

Route::group(['namespace' => 'Botble\Vendorproductunits\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'vendorproductunits', 'as' => 'vendorproductunits.'], function () {
            Route::resource('', 'VendorproductunitsController')->parameters(['' => 'vendorproductunits']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'VendorproductunitsController@deletes',
                'permission' => 'vendorproductunits.destroy',
            ]);
        });
    });

});
