<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentFieldToSourcingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sourcings', function (Blueprint $table) {
            $table->integer('parent_id')->default(0)->after('status');
            $table->integer('user_id')->nullable()->after('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sourcings', function (Blueprint $table) {
            $table->dropColumn('parent_id');
            $table->dropColumn('user_id');
        });
    }
}
