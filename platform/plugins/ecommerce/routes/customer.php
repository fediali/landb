<?php

Route::group(['namespace' => 'Botble\Ecommerce\Http\Controllers\Customers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'customers', 'as' => 'customer.'], function () {
            Route::resource('', 'CustomerController')->parameters(['' => 'customer']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'CustomerController@deletes',
                'permission' => 'customer.destroy',
            ]);
        });
        Route::group(['prefix' => 'customers', 'as' => 'customers.'], function () {
            Route::get('get-list-customers-for-select/{id?}', [
                'as'         => 'get-list-customers-for-select',
                'uses'       => 'CustomerController@getListCustomerForSelect',
                'permission' => 'customer.index',
            ]);

            Route::get('merge-customer-delete/{id}', [
                'as'         => 'merge-customer-delete',
                'uses'       => 'CustomerController@mergeDelete',
                'permission' => 'customer.index',
            ]);

            Route::get('get-list-customers-for-search', [
                'as'         => 'get-list-customers-for-search',
                'uses'       => 'CustomerController@getListCustomerForSearch',
                'permission' => ['customers.index', 'orders.index'],
            ]);
            Route::post('merge-customer', [
                'as'         => 'merge-customer',
                'uses'       => 'CustomerController@mergeCustomer',
                'permission' => 'customers.create',
            ]);

            Route::get('verify-phone/{id}', [
                'as'         => 'verify-phone',
                'uses'       => 'CustomerController@verifyphone',
                'permission' => ['customers.index', 'orders.index'],
            ]);
            Route::get('verify-phone-bulk/{id}', [
                'as'         => 'verify-phone-bulk',
                'uses'       => 'CustomerController@verifyphonebulk',
                'permission' => 'customer.index',
            ]);
            Route::post('update-email/{id}', [
                'as'         => 'update-email',
                'uses'       => 'CustomerController@postUpdateEmail',
                'permission' => 'customer.edit',
            ]);
            Route::get('get-customer-addresses/{id}', [
                'as'         => 'get-customer-addresses',
                'uses'       => 'CustomerController@getCustomerAddresses',
                'permission' => ['customers.index', 'orders.index'],
            ]);
            Route::get('get-addresses/{id}', [
                'as'         => 'get-addresses',
                'uses'       => 'CustomerController@getAddresses',
                'permission' => ['customers.index', 'orders.index'],
            ]);
            Route::get('get-cards/{id}', [
                'as'         => 'get-cards',
                'uses'       => 'CustomerController@getCustomerCard',
                'permission' => ['customers.index', 'orders.index'],
            ]);
            Route::get('get-customer/{id}', [
                'as'         => 'get-customer',
                'uses'       => 'CustomerController@getCustomer',
                'permission' => ['customers.index', 'orders.index'],
            ]);
            Route::get('get-customer-order-numbers/{id}', [
                'as'         => 'get-customer-order-numbers',
                'uses'       => 'CustomerController@getCustomerOrderNumbers',
                'permission' => ['customers.index', 'orders.index'],
            ]);
            Route::post('create-customer-when-creating-order', [
                'as'         => 'create-customer-when-creating-order',
                'uses'       => 'CustomerController@postCreateCustomerWhenCreatingOrder',
                'permission' => 'customers.create',
            ]);
            Route::get('address/{id}', [
                'as'         => 'customer-addresses',
                'uses'       => 'CustomerController@addAddress',
                'permission' => 'customers.create',
            ]);
            Route::get('delete-address', [
                'as'         => 'delete-address',
                'uses'       => 'CustomerController@deleteAddress',
                'permission' => 'customers.create',
            ]);
            Route::post('create-customer-address', [
                'as'         => 'create-customer-address',
                'uses'       => 'CustomerController@postCustomerAddress',
                'permission' => 'customers.create',
            ]);
            Route::post('create-customer-payment', [
                'as'         => 'create-customer-payment',
                'uses'       => 'CustomerController@postCustomerCard',
                'permission' => 'customers.create',
            ]);
            Route::post('update-customer-address', [
                'as'         => 'update-customer-address',
                'uses'       => 'CustomerController@updateCustomerAddress',
                'permission' => 'customers.create',
            ]);
        });
    });
});

Route::group([
    'namespace'  => 'Botble\Ecommerce\Http\Controllers\Customers',
    'middleware' => ['web', 'core', 'customer.guest'],
    'as'         => 'customer.',
], function () {
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login')->name('login.post');

    Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'RegisterController@register')->name('register.post');

    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.request');
    Route::post('password/reset', 'ResetPasswordController@reset')->name('password.email');
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset.update');
});

Route::group([
    'namespace'  => 'Botble\Ecommerce\Http\Controllers\Customers',
    'middleware' => ['web', 'core', 'customer'],
    'prefix'     => 'customer',
    'as'         => 'customer.',
], function () {
    Route::get('logout', 'LoginController@logout')->name('logout');

    Route::get('overview', [
        'as'   => 'overview',
        'uses' => 'PublicController@getOverview',
    ]);

    Route::get('edit-account', [
        'as'   => 'edit-account',
        'uses' => 'PublicController@getEditAccount',
    ]);

    Route::post('edit-account', [
        'as'   => 'edit-account.post',
        'uses' => 'PublicController@postEditAccount',
    ]);

    Route::get('change-password', [
        'as'   => 'change-password',
        'uses' => 'PublicController@getChangePassword',
    ]);

    Route::post('change-password', [
        'as'   => 'post.change-password',
        'uses' => 'PublicController@postChangePassword',
    ]);

    Route::get('orders', [
        'as'   => 'orders',
        'uses' => 'PublicController@getListOrders',
    ]);

    Route::get('orders/view/{id}', [
        'as'   => 'orders.view',
        'uses' => 'PublicController@getViewOrder',
    ]);

    Route::get('order/cancel/{id}', [
        'as'   => 'orders.cancel',
        'uses' => 'PublicController@getCancelOder',
    ]);

    Route::get('address', [
        'as'   => 'address',
        'uses' => 'PublicController@getListAddresses',
    ]);

    Route::get('address/create', [
        'as'   => 'address.create',
        'uses' => 'PublicController@getCreateAddress',
    ]);

    Route::post('address/create', [
        'as'   => 'address.create.post',
        'uses' => 'PublicController@postCreateAddress',
    ]);

    Route::get('address/edit/{id}', [
        'as'   => 'address.edit',
        'uses' => 'PublicController@getEditAddress',
    ]);

    Route::post('address/edit/{id}', [
        'as'   => 'address.edit.post',
        'uses' => 'PublicController@postEditAddress',
    ]);

    Route::get('address/delete/{id}', [
        'as'   => 'address.destroy',
        'uses' => 'PublicController@getDeleteAddress',
    ]);

    Route::get('orders/print/{id}', [
        'as'   => 'print-order',
        'uses' => 'PublicController@getPrintOrder',
    ]);

    Route::post('avatar', [
        'as'   => 'avatar',
        'uses' => 'PublicController@postAvatar',
    ]);
});
