<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('send-order-prod-shipment-email/{order_id}', function($order_id, \Illuminate\Http\Request $request) {
    $order_product_ids = $request->get('order_product_ids');
    $job = new \App\Jobs\SendOrderProdShipmentEmail($order_id, $order_product_ids);
    dispatch_now($job);
});

Route::get('send-manifest-prod-delayed-email/{id}', function($id) {
    $job = new \App\Jobs\SendManifestProdDelayedEmail($id);
    dispatch_now($job);
});

Route::get('send-manifest-prod-shipped-email/{id}', function($id) {
    $job = new \App\Jobs\SendManifestProdShippedEmail($id);
    dispatch_now($job);
});
