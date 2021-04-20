<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalesCommissionFieldsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->float('ecom_commission_percent')->after('commission_percentage')->default(0)->nullable();
            $table->integer('salesperson_id')->after('name_initials')->default(0)->nullable();
        });

        Schema::table('ec_orders', function (Blueprint $table) {
            $table->float('sales_commission_percent')->after('payment_id')->default(0)->nullable();
            $table->float('sales_commission_amount')->after('payment_id')->default(0)->nullable();
            $table->integer('salesperson_id')->after('payment_id')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ecom_commission_percent');
            $table->dropColumn('salesperson_id');
        });

        Schema::table('ec_orders', function (Blueprint $table) {
            $table->dropColumn('sales_commission_percent');
            $table->dropColumn('sales_commission_amount');
            $table->dropColumn('salesperson_id');
        });
    }
}
