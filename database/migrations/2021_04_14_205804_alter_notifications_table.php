<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::dropIfExists('notifications');
      Schema::create('notifications', function (Blueprint $table){
        $table->increments('id');
        $table->integer('sender_id');
        $table->string('url');
        $table->string('action');
        $table->integer('ref_id');
        $table->string('message');
        $table->string('status')->default('active');
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
        //
    }
}
