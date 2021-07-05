<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_searches', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index('user_searches_user_id_fk');
            $table->string('search_type', 100)->default('orders');
            $table->string('name', 255)->default('today')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
        Schema::create('user_search_items', function (Blueprint $table) {
            $table->id();
            $table->integer('user_search_id')->index('user_search_items_user_search_id_fk');
            $table->string('key', 150);
            $table->string('value', 150);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_searches');
        Schema::dropIfExists('user_search_items');
    }
}
