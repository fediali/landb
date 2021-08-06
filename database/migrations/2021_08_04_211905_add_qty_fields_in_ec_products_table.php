<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQtyFieldsInEcProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_products', function (Blueprint $table) {
            $table->integer('in_cart_qty')->default(0)->nullable()->after('quantity');
            $table->integer('reorder_qty')->default(0)->nullable()->after('quantity');
            $table->integer('pre_order_qty')->default(0)->nullable()->after('quantity');
            $table->integer('sold_qty')->default(0)->nullable()->after('quantity');
            $table->integer('single_qty')->default(0)->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_products', function (Blueprint $table) {
            $table->dropColumn('single_qty');
            $table->dropColumn('pre_order_qty');
            $table->dropColumn('sold_qty');
            $table->dropColumn('reorder_qty');
            $table->dropColumn('in_cart_qty');
        });
    }
}
