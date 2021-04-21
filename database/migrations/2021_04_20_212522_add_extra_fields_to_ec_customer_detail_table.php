<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToEcCustomerDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_customer_detail', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->integer('preferred_communication')->nullable();
            $table->string('events_attended')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_customer_detail', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('preferred_communication');
            $table->dropColumn('events_attended');
        });
    }
}
