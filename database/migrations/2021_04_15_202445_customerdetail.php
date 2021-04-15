<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Customerdetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec_customer_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->index('customer_detail_fk');
            $table->integer('sales_tax_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('company')->nullable();
            $table->string('customer_type')->nullable();
            $table->string('store_facebook')->nullable();
            $table->string('store_instagram')->nullable();
            $table->string('mortar_address')->nullable();
            $table->tinyInteger('newsletter')->default(0)->nullable();
            $table->integer('hear_us')->nullable();
            $table->string('comments')->nullable();
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
        Schema::dropIfExists('ec_customer_detail');
    }
}
