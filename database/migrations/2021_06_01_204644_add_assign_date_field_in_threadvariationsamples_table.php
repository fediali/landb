<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignDateFieldInThreadvariationsamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('threadvariationsamples', function (Blueprint $table) {
            $table->timestamp('assign_date')->nullable()->after('thread_variation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('threadvariationsamples', function (Blueprint $table) {
            $table->dropColumn('assign_date');
        });
    }
}
