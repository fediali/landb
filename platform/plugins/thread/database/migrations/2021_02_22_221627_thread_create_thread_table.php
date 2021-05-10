<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ThreadCreateThreadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('designer_id')->index('th_designer_id_fk');
            $table->integer('vendor_id')->nullable()->index('th_vendor_id_fk');
            $table->integer('season_id')->index('th_season_id_fk');
            $table->string('order_no', 255)->nullable();
            $table->string('order_status', 100)->default('new');
            $table->string('pp_sample', 50)->nullable()->default('no');
            $table->string('pp_sample_size', 100)->nullable();
            $table->timestamp('pp_sample_date')->nullable();
            $table->string('material', 150)->nullable();
            $table->string('sleeve', 150)->nullable();
            $table->string('label', 150)->nullable();
            $table->string('shipping_method', 100)->default('sea');
            $table->timestamp('order_date')->useCurrent();
            $table->timestamp('ship_date')->useCurrent();
            $table->timestamp('cancel_date')->useCurrent();
            $table->tinyInteger('is_denim')->default(0);
            $table->string('inseam', 150)->nullable();
            $table->integer('fit_id')->nullable()->index('th_fit_id_fk');
            $table->integer('rise_id')->nullable()->index('th_rise_id_fk');
            $table->integer('fabric_id')->nullable()->index('th_fabric_id_fk');
            $table->string('fabric_print_direction', 150)->nullable();
            //$table->string('spec_file', 255)->nullable();
            $table->string('status', 60)->default('published');
            $table->integer('business_id')->default(1);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categories_threads', function (Blueprint $table) {
            $table->integer('thread_id')->index('cat_th_thread_id_fk');
            $table->string('sku', 255);
            $table->string('category_type', 100)->default('regular');
            $table->integer('product_category_id')->index('cat_th_product_category_id_fk');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('threads');
        Schema::dropIfExists('categories_threads');
    }
}
