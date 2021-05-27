<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadPvtCatSizesQtyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thread_pvt_cat_sizes_qty', function (Blueprint $table) {
            $table->id();
            $table->integer('thread_id');
            $table->integer('product_category_id');
            $table->integer('category_size_id');
            $table->integer('qty');
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
        Schema::dropIfExists('thread_pvt_cat_sizes_qty');
    }
}
