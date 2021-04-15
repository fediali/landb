<?php

Route::group(['namespace' => 'Botble\Ecommerce\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'orders-import', 'as' => 'orders-import.'], function () {
            Route::resource('', 'OrderImportController')->parameters(['' => 'order-import']);


            Route::get('orders-import', [
                'as'         => 'orders-import',
                'uses'       => 'OrderImportController@index',
                'permission' => 'orders-import.index',
            ]);


        });

    });
});
