<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ThreadvariationsamplesCreateThreadvariationsamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threadvariationsamples', function (Blueprint $table) {
            $table->id();
            $table->integer('photographer_id')->index('tvs_photographer_id');
            $table->integer('thread_id')->index('tvs_thread_id');
            $table->integer('thread_variation_id')->index('tvs_thread_variation_id');
            $table->string('status', 60)->default('published');
            $table->integer('business_id')->default(1);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('thread_variation_sample_media', function (Blueprint $table) {
            $table->integer('thread_variation_sample_id')->index('tvsm_sample_id');
            $table->string('media', 255);
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
        Schema::dropIfExists('threadvariationsamples');
        Schema::dropIfExists('thread_variation_sample_media');
    }
}
