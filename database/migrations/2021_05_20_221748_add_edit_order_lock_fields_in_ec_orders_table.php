<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditOrderLockFieldsInEcOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_orders', function (Blueprint $table) {
            $table->integer('editing_by')->nullable();
            $table->timestamp('editing_started_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_orders', function (Blueprint $table) {
            $table->dropColumn('editing_by');
            $table->dropColumn('editing_started_at');
        });
    }
}
