<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorProductFieldsInThreadordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('threadorders', function (Blueprint $table) {
            $table->integer('vendor_product_id')->nullable()->default(0)->index('th_vendor_product_id_fk')->after('elastic_waste_pant');
        });
        Schema::table('thread_order_variations', function (Blueprint $table) {
            $table->float('per_piece_qty')->default(0)->after('product_category_id');
            $table->integer('product_unit_id')->default(0)->index('ct_unit_id_fk')->after('product_category_id');
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
            $table->dropColumn('vendor_product_id');
        });
        Schema::table('thread_order_variations', function (Blueprint $table) {
            $table->dropColumn('per_piece_qty');
            $table->dropColumn('product_unit_id');
        });
    }
}
