<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitFieldsInProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_product_categories', function (Blueprint $table) {
            $table->integer('product_unit_id')->default(0)->index('pc_unit_id_fk')->after('is_plus_cat');
            $table->float('per_piece_qty')->default(0)->after('is_plus_cat');
            $table->float('impact_price')->default(0)->after('is_plus_cat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_product_categories', function (Blueprint $table) {
            $table->dropColumn('impact_price');
            $table->dropColumn('per_piece_qty');
            $table->dropColumn('product_unit_id');
        });
    }
}
