<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThreadStatusFieldInThreadordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('threadorders', function (Blueprint $table) {
            $table->string('thread_status',100)->default('new')->after('order_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('threadorders', function (Blueprint $table) {
            $table->dropColumn('thread_status');
        });
    }
}
