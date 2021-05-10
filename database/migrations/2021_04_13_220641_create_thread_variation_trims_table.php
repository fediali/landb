<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadVariationTrimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thread_variation_trims', function (Blueprint $table) {
            $table->id();
            $table->integer('thread_variation_id')->index('tvt_tv_id_fk');
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
        Schema::dropIfExists('thread_variation_trims');
    }
}
