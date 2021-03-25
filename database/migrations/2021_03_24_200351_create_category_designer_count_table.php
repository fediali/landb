<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryDesignerCountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_designer_count', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index('cdc_user_id_fk');
            $table->integer('product_category_id')->index('cdc_prod_cat_id_fk');
            $table->integer('count');
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
        Schema::dropIfExists('category_designer_count');
    }
}
