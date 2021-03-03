<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PrintdesignsCreatePrintdesignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printdesigns', function (Blueprint $table) {
            $table->id();
            $table->integer('designer_id')->index('pd_designer_id_fk');
            $table->string('name', 255);
            $table->string('sku', 255);
            $table->string('file', 255);
            $table->string('file_type', 255)->default('jpg');
            $table->string('status', 60)->default('published');
            $table->integer('business_id')->default(1);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
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
        Schema::dropIfExists('printdesigns');
    }
}
