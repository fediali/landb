<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInEcOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::connection('mysql2')->table('hw_orders', function (Blueprint $table) {
//            $table->string('copy_order_from', 255)->nullable();
//            $table->string('copy_order_id', 255)->nullable();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::connection('mysql2')->table('hw_orders', function (Blueprint $table) {
//            $table->dropColumn('copy_order_from');
//            $table->dropColumn('copy_order_id');
//        });
    }
}
