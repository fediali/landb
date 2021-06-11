<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPtypeFieldsInEcProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_products', function (Blueprint $table) {
            $table->integer('restock')->default(0)->nullable();
            $table->integer('new_label')->default(0)->nullable();
            $table->tinyInteger('usa_made')->default(0)->nullable();
            $table->string('ptype', 20)->nullable();
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
            $table->dropColumn('restock');
            $table->dropColumn('new_label');
            $table->dropColumn('usa_made');
            $table->dropColumn('ptype');
        });
    }
}
