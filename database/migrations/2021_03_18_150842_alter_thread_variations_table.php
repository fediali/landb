<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterThreadVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('thread_variations', function (Blueprint $table) {
        $table->integer('regular_qty')->nullable()->change();
        $table->integer('plus_qty')->nullable()->change();
        $table->integer('cost')->nullable()->change();
        $table->string('notes')->nullable()->change();
        $table->integer('wash_id')->nullable();
        $table->string('file')->nullable();
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
      Schema::table('thread_variations', function (Blueprint $table) {
        $table->dropColumn('wash_id');
        $table->dropColumn('file');
      });
    }
}
