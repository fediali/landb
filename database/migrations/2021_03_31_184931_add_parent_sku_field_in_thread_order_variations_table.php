<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentSkuFieldInThreadOrderVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('thread_order_variations', function (Blueprint $table) {
            $table->string('parent_sku',150)->index('tov_parent_sku_fk')->after('sku');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thread_order_variations', function (Blueprint $table) {
            $table->dropColumn('parent_sku');
        });
    }
}
