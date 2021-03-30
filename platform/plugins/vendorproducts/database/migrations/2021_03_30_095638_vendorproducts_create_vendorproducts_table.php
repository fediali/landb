<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class VendorproductsCreateVendorproductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendorproducts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->float('quantity')->default(0);
            $table->integer('product_unit_id')->index('vp_unit_id_fk');
            $table->string('status', 60)->default('published');
            $table->integer('business_id')->default(1);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('vendorproducts_history', function (Blueprint $table) {
            $table->id();
            $table->integer('vendor_product_id')->index('vph_product_id_fk');
            $table->integer('thread_order_id')->index('vph_order_id_fk');
            $table->float('quantity')->default(0);
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
        Schema::dropIfExists('vendorproducts');
    }
}
