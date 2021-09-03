<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentStatusFieldInEcOrderPreauthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_order_preauth', function (Blueprint $table) {
            $table->string('payment_status', 255)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_order_preauth', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
}
