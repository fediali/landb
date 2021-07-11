<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDashboardFieldInMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->tinyInteger('dashboard_menu')->default(0)->nullable()->after('status');
        });

        Schema::table('menu_nodes', function (Blueprint $table) {
            $table->string('plugin_id', 255)->nullable()->after('parent_id');
            $table->text('permissions')->nullable()->after('has_child');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('dashboard_menu');
        });
        Schema::table('menu_nodes', function (Blueprint $table) {
            $table->dropColumn('plugin_id');
            $table->dropColumn('permissions');
        });
    }
}
