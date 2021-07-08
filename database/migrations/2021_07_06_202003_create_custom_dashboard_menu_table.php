<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomDashboardMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_dashboard_menu', function (Blueprint $table) {
            $table->id();
            $table->string('menu_id', 255);
            $table->tinyInteger('priority')->default(99)->nullable();
            $table->string('parent_id', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('icon', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->text('permissions')->nullable();
            $table->tinyInteger('status')->default(0)->nullable();
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
        Schema::dropIfExists('custom_dashboard_menu');
    }
}
