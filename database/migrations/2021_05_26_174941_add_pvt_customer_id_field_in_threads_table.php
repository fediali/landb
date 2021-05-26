<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPvtCustomerIdFieldInThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->integer('pvt_customer_id')->nullable()->default(0);
        });
        Schema::table('threadorders', function (Blueprint $table) {
            $table->integer('pvt_customer_id')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn('pvt_customer_id');
        });
        Schema::table('threadorders', function (Blueprint $table) {
            $table->dropColumn('pvt_customer_id');
        });
    }
}
