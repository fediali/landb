<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AccountingsystemCreateAccountingsystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accountingsystems', function (Blueprint $table) {
            $table->id();
            $table->string('money', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->float('amount')->default(0)->nullable();

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
        Schema::dropIfExists('accountingsystems');
    }
}
