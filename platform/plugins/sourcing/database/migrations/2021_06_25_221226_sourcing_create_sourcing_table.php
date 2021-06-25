<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class SourcingCreateSourcingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sourcings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->text('file')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 60)->default('pending');
            $table->integer('business_id')->default(1);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('sourcings');
    }
}
