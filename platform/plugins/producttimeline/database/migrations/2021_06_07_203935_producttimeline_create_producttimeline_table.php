<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ProducttimelineCreateProducttimelineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producttimelines', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('status', 60)->default('published');
            $table->datetime('schedule_date');
            $table->integer('business_id')->default(1);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('producttimelinesdetail', function (Blueprint $table) {
            $table->id();
            $table->integer('product_timeline_id');
            $table->text('product_image');
            $table->text('product_link');
            $table->text('product_desc');
            $table->softDeletes();
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
        Schema::dropIfExists('producttimelines');
        Schema::dropIfExists('producttimelinesdetail');
    }
}
