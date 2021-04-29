<?php

// Custom routes
// You can delete this route group if you don't need to add your custom routes.
Route::group(['namespace' => 'Theme\Landb\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

    });
});
Route::group(['namespace' => 'Theme\Landb\Http\Controllers', 'middleware' => ['web', 'core', 'customer']],function () {
  Route::get('logout', 'AuthController@logout')->name('public.logout');

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

  Route::get('/product/detail/{id}', 'ProductsController@getDetails')
      ->name('public.singleProduct');

  Route::get('/product/add/wishlist/{id}', 'WishlistController@addToWishlist')
      ->name('public.add_to_wishlist');

  Route::post('/add_to_cart', 'CartController@createCart')
      ->name('public.cart.add_to_cart');

  Route::post('/update_cart_quantity', 'CartController@updateCartQuanity')
      ->name('public.cart.update_cart');

  Route::post('/checkout', 'CheckoutController@proceedPayment')
      ->name('public.cart.order_checkout');
});
Theme::routes();

Route::group(['namespace' => 'Theme\Landb\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

        Route::get('/', 'LandbController@getIndex')
            ->name('public.index');

        Route::get('/login', 'AuthController@showLoginForm')
            ->name('customer.login');

        Route::get('/register', 'RegisterController@showRegisterForm')
            ->name('public.register');

        Route::get('/products', 'ProductsController@getIndex')
            ->name('public.products');

        Route::get('/product-categories/{category}', 'ProductsController@productsByCategory')
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

    });
});
