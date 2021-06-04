<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerProductDemandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_product_demand', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->index('cpd_customer_id');
            $table->integer('product_id')->index('cpd_product_id');
            $table->integer('variation_id')->index('cpd_variation_id')->default(0)->nullable();
            $table->integer('demand_qty')->default(0)->nullable();
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
        Schema::dropIfExists('customer_product_demand');
    }
}
