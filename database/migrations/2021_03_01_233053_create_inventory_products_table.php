<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_products', function (Blueprint $table) {
            $table->id();
            $table->integer('inventory_id')->index('ip_inventory_id_fk');
            $table->integer('product_id')->index('ip_product_id_fk');
            $table->string('sku')->index('ipp_sku_fk');
            $table->string('upc')->index('ipp_upc_fk')->nullable();
            $table->string('barcode')->nullable();
            $table->string('is_variation')->nullable();
            $table->integer('ecom_qty')->default(0)->nullable();
            $table->integer('ordered_qty')->default(0)->nullable();
            $table->integer('received_qty')->default(0)->nullable();
            $table->timestamps();
        });

        /*Schema::create('inventory_product_cat_qty', function (Blueprint $table) {
            $table->integer('inventory_product_id')->index('ipcq_inventory_prod_id_fk');
            $table->integer('product_category_id')->index('ipcq_prod_cat_id_fk');
            $table->integer('loose_qty')->default(0)->nullable();
        });*/

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_products');
        //Schema::dropIfExists('inventory_product_cat_qty');
    }
}
