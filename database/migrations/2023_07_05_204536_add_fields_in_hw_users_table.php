<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInHwUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql3')->table('hw_users', function (Blueprint $table) {
            $table->string('copy_customer_from', 255)->nullable();
            $table->string('copy_customer_id', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql3')->table('hw_users', function (Blueprint $table) {
            $table->dropColumn('copy_customer_from');
            $table->dropColumn('copy_customer_id');
        });
    }
}
