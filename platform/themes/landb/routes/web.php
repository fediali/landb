<?php

// Custom routes
// You can delete this route group if you don't need to add your custom routes.
Route::group(['namespace' => 'Theme\Landb\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::get('/orderr', 'LandbController@orderSuccess');

    Route::get('/product-timeline', 'ProductsController@timeline')
      ->name('public.cart.timeline');

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

    });
});
Route::group(['namespace' => 'Theme\Landb\Http\Controllers', 'middleware' => ['web', 'core', 'customer']], function () {
  Route::get('logout', 'AuthController@logout')->name('public.logout');
});
Route::group(['namespace' => 'Theme\Landb\Http\Controllers', 'middleware' => ['web', 'core', 'customer', 'verifiedCustomer']], function () {

    Route::get('/cart', 'CartController@getIndex')
        ->name('public.cart_index');

    Route::get('/wishlist', 'WishlistController@getIndex')
        ->name('public.wishlist_index');

    Route::get('/checkout/{token}', 'CheckoutController@getCheckoutIndex')
        ->name('public.checkout_index');

    Route::get('/checkout/success', 'CheckoutController@getCheckoutSuccess')
        ->name('public.checkout_success');

    Route::get('/payment/status', 'CheckoutController@getPayPalStatus')
        ->name('public.paypal_status');

    Route::get('/products/{slug?}', 'ProductsController@getDetails')
        ->name('public.singleProduct');

    Route::get('/product/add/wishlist/{id}', 'WishlistController@addToWishlist')
        ->name('public.add_to_wishlist');

    Route::post('/add_to_cart', 'CartController@createCart')
        ->name('public.cart.add_to_cart');

    Route::post('/update_cart_quantity', 'CartController@updateCartQuanity')
        ->name('public.cart.update_cart');

    Route::post('/checkout', 'CheckoutController@proceedPayment')
        ->name('public.cart.order_checkout');

    Route::get('/order/success/{id}', 'OrderController@success')
        ->name('public.order.success');

    Route::get('/order/status/{id}', 'OrderController@index')
        ->name('public.order.status');

    Route::get('/product-timeline/{id?}', 'ProductsController@timeline')
        ->name('public.cart.timeline');

    Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
        Route::get('/edit-account', [
            'as'   => 'edit-account',
            'uses' => 'CustomerController@edit'
        ]);
        Route::get('/overview', [
            'as'   => 'overview',
            'uses' => 'CustomerController@show'
        ]);

        Route::get('/update-default', [
            'as'   => 'update-default',
            'uses' => 'CustomerController@updateDefaultById'
        ]);

        Route::post('/edit-account/{type}', [
            'as'   => 'edit-account-post',
            'uses' => 'CustomerController@update'
        ]);
    });
});
Theme::routes();

Route::group(['namespace' => 'Theme\Landb\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

        Route::get('/customer/verify', [
            'as'   => 'customer.pendingNotification',
            'uses' => 'CustomerController@pendingNotification'
        ]);

        Route::get('customer/contract-form', [
            'as'   => 'customer.contract-form',
            'uses' => 'CustomerController@contractForm'
        ]);

        Route::get('/', 'LandbController@getIndex')
            ->name('public.index');

        Route::get('/login', 'AuthController@showLoginForm')
            ->name('customer.login');

        Route::get('/register', 'RegisterController@showRegisterForm')
            ->name('public.register');

        Route::get('/products', 'ProductsController@getIndex')
            ->name('public.products');

        Route::get(SlugHelper::getPrefix(ProductCategory::class, 'product-categories') . '/{slug}', 'ProductsController@productsByCategory')
            ->name('public.productsByCategory');

        Route::get('sitemap.xml', 'LandbController@getSiteMap')
            ->name('public.sitemap');

        Route::get('{slug?}' . config('core.base.general.public_single_ending_url'), 'LandbController@getView')
            ->name('public.single');

        /*POST ROUTES*/
        Route::post('/login', 'AuthController@login')
            ->name('public.login.post');

        Route::post('/register', 'RegisterController@register')
            ->name('public.register.post');


      Route::group(['prefix' => 'ajax', 'as' => 'ajax.'], function () {
        Route::get('get_states', [
            'as'   => 'getStates',
            'uses' => 'CustomerController@getStates'
        ]);
        Route::get('get_countries', [
            'as'   => 'getCountries',
            'uses' => 'CustomerController@getCountries'
        ]);
      });

    });
});
