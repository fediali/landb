<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderImportUploadID extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec_order_import_upload', function (Blueprint $table) {
            $table->id();
            $table->string('file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('ec_order_import', function (Blueprint $table) {
            $table->integer('order_import_upload_id')->nullable();
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
        Schema::dropIfExists('ec_order_import_upload');
    }
}
