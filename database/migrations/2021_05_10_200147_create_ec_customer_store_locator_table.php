<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcCustomerStoreLocatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec_customer_store_locator', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->string('locator_company');
            $table->string('locator_phone');
            $table->string('locator_website');
            $table->string('locator_address');
            $table->string('locator_city');
            $table->string('locator_country');
            $table->string('locator_state');
            $table->integer('locator_zip_code');
            $table->string('locator_customer_type');
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
        Schema::dropIfExists('ec_customer_store_locator');
    }
}
