<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteInCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_customers', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ec_orders', function (Blueprint $table) {
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
        Schema::table('ec_customers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ec_orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
