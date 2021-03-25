<?php

Route::group(['namespace' => 'Botble\Threadorders\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'threadorders', 'as' => 'threadorders.'], function () {
            Route::resource('', 'ThreadordersController')->parameters(['' => 'threadorders']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ThreadordersController@deletes',
                'permission' => 'threadorders.destroy',
            ]);
            Route::get('create-thread-order/{id}', [
                'as'         => 'createThreadOrder',
                'uses'       => 'ThreadordersController@createThreadOrder',
                'permission' => 'threadorders.create',
            ]);
            Route::post('create-thread-order/{id}', [
                'as'         => 'storeThreadOrder',
                'uses'       => 'ThreadordersController@storeThreadOrder',
                'permission' => 'threadorders.create',
            ]);
            Route::post('change-status', [
                'as'         => 'changeStatus',
                'uses'       => 'ThreadordersController@changeStatus',
                'permission' => 'thread.create',
            ]);
            Route::get('orderItem/{id}', [
              'as'         => 'orderItem',
              'uses'       => 'ThreadordersController@pushToEcommerce',
              'permission' => 'threadorders.create',
            ]);
            Route::get('detail/{id}', [
                'as'   => 'threadOrderDetail',
                'uses' => 'ThreadordersController@showThreadOrderDetail',
            ]);
        });
    });

});
