<?php

Route::group(['namespace' => 'Botble\Ecommerce\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'orders-import', 'as' => 'orders-import.'], function () {
            Route::resource('', 'OrderImportController')->parameters(['' => 'order-import']);



            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'OrderImportController@deletes',
                'permission' => 'orders.destroy',
            ]);

            Route::get('reorder', [
                'as'         => 'reorder',
                'uses'       => 'OrderImportController@getReorder',
                'permission' => 'orders.create',
            ]);

            Route::get('generate-invoice/{id}', [
                'as'         => 'generate-invoice',
                'uses'       => 'OrderImportController@getGenerateInvoice',
                'permission' => 'orders.edit',
            ]);

            Route::post('confirm', [
                'as'         => 'confirm',
                'uses'       => 'OrderImportController@postConfirm',
                'permission' => 'orders.edit',
            ]);

            Route::post('send-order-confirmation-email/{id}', [
                'as'         => 'send-order-confirmation-email',
                'uses'       => 'OrderImportController@postResendOrderConfirmationEmail',
                'permission' => 'orders.edit',
            ]);

            Route::post('create-shipment/{id}', [
                'as'         => 'create-shipment',
                'uses'       => 'OrderImportController@postCreateShipment',
                'permission' => 'orders.edit',
            ]);

            Route::post('cancel-shipment/{id}', [
                'as'         => 'cancel-shipment',
                'uses'       => 'OrderImportController@postCancelShipment',
                'permission' => 'orders.edit',
            ]);

            Route::post('update-shipping-address/{id}', [
                'as'         => 'update-shipping-address',
                'uses'       => 'OrderImportController@postUpdateShippingAddress',
                'permission' => 'orders.edit',
            ]);

            Route::post('cancel-order/{id}', [
                'as'         => 'cancel',
                'uses'       => 'OrderImportController@postCancelOrder',
                'permission' => 'orders.edit',
            ]);

            Route::get('print-shipping-order/{id}', [
                'as'         => 'print-shipping-order',
                'uses'       => 'OrderImportController@getPrintShippingOrder',
                'permission' => 'orders.edit',
            ]);

            Route::post('confirm-payment/{id}', [
                'as'         => 'confirm-payment',
                'uses'       => 'OrderImportController@postConfirmPayment',
                'permission' => 'orders.edit',
            ]);

            Route::get('get-shipment-form/{id}', [
                'as'         => 'get-shipment-form',
                'uses'       => 'OrderImportController@getShipmentForm',
                'permission' => 'orders.edit',
            ]);

            Route::post('refund/{id}', [
                'as'         => 'refund',
                'uses'       => 'OrderImportController@postRefund',
                'permission' => 'orders.edit',
            ]);

            Route::get('get-available-shipping-methods', [
                'as'         => 'get-available-shipping-methods',
                'uses'       => 'OrderImportController@getAvailableShippingMethods',
                'permission' => 'orders.edit',
            ]);

            Route::post('coupon/apply', [
                'as'         => 'apply-coupon-when-creating-order',
                'uses'       => 'OrderImportController@postApplyCoupon',
                'permission' => 'orders.create',
            ]);

        });

        Route::group(['prefix' => 'incomplete-orders', 'as' => 'orders.'], function () {
            Route::get('', [
                'as'         => 'incomplete-list',
                'uses'       => 'OrderImportController@getIncompleteList',
                'permission' => 'orders.index',
            ]);

            Route::get('view/{id}', [
                'as'         => 'view-incomplete-order',
                'uses'       => 'OrderImportController@getViewIncompleteOrder',
                'permission' => 'orders.index',
            ]);

            Route::post('send-order-recover-email/{id}', [
                'as'         => 'send-order-recover-email',
                'uses'       => 'OrderImportController@postSendOrderRecoverEmail',
                'permission' => 'orders.index',
            ]);
        });
    });
});
