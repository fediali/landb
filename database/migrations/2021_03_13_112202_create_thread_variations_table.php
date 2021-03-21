<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thread_variations', function (Blueprint $table) {
            $table->id();
            $table->integer('thread_id');
            $table->string('name');
            $table->integer('print_id');
            $table->integer('regular_qty');
            $table->integer('plus_qty');
            $table->integer('cost');
            $table->string('notes');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('business_id')->default(1);
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
        Schema::dropIfExists('thread_variations');
    }
}
