<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCardIdFieldInEcOrderPreauthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_order_preauth', function (Blueprint $table) {
            $table->string('card_id', 255)->nullable()->after('order_id');
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
            $table->dropColumn('card_id');
        });
    }
}
