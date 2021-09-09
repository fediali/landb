<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaypalEmailInEcCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_customers', function (Blueprint $table) {
            $table->string('paypal_email', 255)->after('dob')->nullable();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->string('paypal_email', 255)->after('payment_channel')->nullable();
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
            $table->dropColumn('paypal_email');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('paypal_email');
        });
    }
}
