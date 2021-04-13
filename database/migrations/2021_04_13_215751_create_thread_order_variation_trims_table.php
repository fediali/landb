<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadOrderVariationTrimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thread_order_variation_trims', function (Blueprint $table) {
            $table->id();
            $table->integer('thread_order_variation_id')->index('tovt_tov_id_fk');
            $table->string('trim_image',255);
            $table->string('trim_note',255);
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
        Schema::dropIfExists('thread_order_variation_trims');
    }
}
