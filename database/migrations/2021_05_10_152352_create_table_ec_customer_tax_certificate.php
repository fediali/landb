<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEcCustomerTaxCertificate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec_customer_tax_certificate', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->string('purchaser_name');
            $table->string('purchaser_phone');
            $table->string('purchaser_address');
            $table->string('purchaser_city');
            $table->integer('permit_no');
            $table->integer('registration_no');
            $table->text('business_description');
            $table->text('items_description');
            $table->string('title');
            $table->date('date');
            $table->text('purchaser_sign');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ec_customer_tax_certificate');
    }
}
