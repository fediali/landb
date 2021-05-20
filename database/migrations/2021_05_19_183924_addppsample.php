<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Addppsample extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thread_variation_pp_sample', function (Blueprint $table) {
            $table->id();
            $table->integer('thread_variation_id')->index('variation_id');
            $table->date('receive_date')->nullable();
            $table->text('comments')->nullable();
            $table->string('status')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thread_variation_pp_sample');
    }
}
