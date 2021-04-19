<?php

Route::group(['namespace' => 'Botble\Inventory\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'inventories', 'as' => 'inventory.'], function () {
            Route::resource('', 'InventoryController')->parameters(['' => 'inventory']);
            Route::get('/get/barcode/product', 'InventoryController@getProductByBarcode')->name('getProductByBarcode');
            Route::get('/pushToEcommerce/{id}', [
                'as'         => 'pushToEcommerce',
                'uses'       => 'InventoryController@pushToEcommerce',
                'permission' => 'inventory.create',
            ]);
            Route::get('/release-product/{inv_id}/{prod_id}', [
                'as'         => 'releaseProduct',
                'uses'       => 'InventoryController@releaseProduct',
                'permission' => 'inventory.edit',
            ]);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'InventoryController@deletes',
                'permission' => 'inventory.destroy',
            ]);
        });
    });

});
