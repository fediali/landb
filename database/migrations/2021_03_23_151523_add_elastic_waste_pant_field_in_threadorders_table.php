<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddElasticWastePantFieldInThreadordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('threadorders', function (Blueprint $table) {
            $table->tinyInteger('elastic_waste_pant')->after('cancel_date')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('threadorders', function (Blueprint $table) {
            $table->dropColumn('elastic_waste_pant');
        });
    }
}
