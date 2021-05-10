<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegPackQtyPlusPackQtyFieldsInThreadordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('threadorders', function (Blueprint $table) {
            $table->tinyInteger('reg_pack_qty')->after('fabric_print_direction')->nullable()->default(0);
            $table->tinyInteger('plus_pack_qty')->after('fabric_print_direction')->nullable()->default(0);
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
            $table->dropColumn('reg_pack_qty');
            $table->dropColumn('plus_pack_qty');
        });
    }
}
