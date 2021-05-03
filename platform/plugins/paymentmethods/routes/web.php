<?php

Route::group(['namespace' => 'Botble\Paymentmethods\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'paymentmethods', 'as' => 'paymentmethods.'], function () {
            Route::resource('', 'PaymentmethodsController')->parameters(['' => 'paymentmethods']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'PaymentmethodsController@deletes',
                'permission' => 'paymentmethods.destroy',
            ]);
        });
    });

});
