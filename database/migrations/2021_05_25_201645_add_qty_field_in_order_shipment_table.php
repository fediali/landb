<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQtyFieldInOrderShipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products_shipment_verification', function (Blueprint $table) {
            $table->integer('qty')->default(0)->nullable()->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_products_shipment_verification', function (Blueprint $table) {
            $table->dropColumn('qty');
        });
    }
}
