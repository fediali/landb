<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQtyAllotmentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qty_allotment_history', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->index('qah_prod_id_fk');
            $table->integer('online_sales_qty')->default(0)->nullable();
            $table->integer('in_person_sales_qty')->default(0)->nullable();
            $table->string('reference',255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qty_allotment_history');
    }
}
