<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpcFieldsInThreadOrderVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('thread_order_variations', function (Blueprint $table) {
            $table->string('upc', 150)->nullable();
            $table->string('barcode',255)->nullable();
        });
        Schema::table('ec_products', function (Blueprint $table) {
            $table->string('upc', 150)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thread_order_variations', function (Blueprint $table) {
            $table->dropColumn('upc');
            $table->dropColumn('barcode');
        });
        Schema::table('ec_products', function (Blueprint $table) {
            $table->dropColumn('upc');
        });
    }
}
