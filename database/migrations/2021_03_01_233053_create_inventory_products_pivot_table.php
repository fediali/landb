<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryProductsPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_products_pivot', function (Blueprint $table) {
            $table->id();
            $table->integer('inventory_id')->index('ipp_inventory_id_fk');
            $table->integer('product_id')->index('ipp_product_id_fk');
            $table->string('sku')->index('ipp_sku_fk');
            $table->integer('barcode')->nullable();
            $table->integer('ecom_pack_qty')->default(0)->nullable();
            $table->integer('ordered_pack_qty')->default(0)->nullable();
            $table->integer('received_pack_qty')->default(0)->nullable();
            $table->integer('loose_qty')->default(0)->nullable();
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
        Schema::dropIfExists('inventory_products_pivot');
    }
}
