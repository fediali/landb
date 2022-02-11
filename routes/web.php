<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'html'], function () {
  Route::get('/home', function (){
    return view('index');
  });
});


Route::post('send-order-prod-shipment-email/{order_id}', function(\Illuminate\Http\Request $request) {
    $order_id = $request->get('order_id');
    $order_product_ids = $request->get('order_product_ids');
    $job = new \App\Jobs\SendOrderProdShipmentEmail($order_id, $order_product_ids);
    dispatch($job);
});
