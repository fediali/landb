<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductsShipmentVerificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products_shipment_verification', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->index('opsv_order_id');
            $table->integer('product_id')->index('opsv_product_id');
            $table->tinyInteger('is_verified')->default(0);
            $table->integer('created_by')->index('opsv_created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_products_shipment_verification');
    }
}
