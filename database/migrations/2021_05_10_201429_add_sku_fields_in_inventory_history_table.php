<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSkuFieldsInInventoryHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_history', function (Blueprint $table) {
            $table->string('sku', 255)->nullable()->after('product_id');
            $table->integer('parent_product_id')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_history', function (Blueprint $table) {
            $table->dropColumn('sku');
            $table->dropColumn('parent_product_id');
        });
    }
}
