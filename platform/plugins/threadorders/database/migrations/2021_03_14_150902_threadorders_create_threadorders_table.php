<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ThreadordersCreateThreadordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threadorders', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('thread_id')->index('th_thread_id_fk');
            $table->integer('designer_id')->index('th_designer_id_fk');
            $table->integer('vendor_id')->nullable()->index('th_vendor_id_fk');
            $table->integer('season_id')->index('th_season_id_fk');
            $table->string('order_no', 255);
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
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('thread_order_variations', function (Blueprint $table) {
            $table->integer('thread_order_id')->index('th_ord_thread_order_id_fk');
            $table->string('category_type', 100)->default('regular');
            $table->integer('product_category_id')->index('th_ord_product_category_id_fk');
            $table->integer('thread_variation_id')->index('th_ord_thread_variation_id_fk');
            $table->integer('print_design_id')->index('th_ord_print_design_id_fk');
            $table->string('name', 255);
            $table->string('sku', 255);
            $table->integer('quantity');
            $table->float('cost');
            $table->string('notes', 255);
        });

        Schema::create('thread_order_variation_fabrics', function (Blueprint $table) {
            $table->integer('thread_order_variation_id')->index('th_var_fab_thread_order_variation_id_fk');
            $table->integer('print_design_id')->index('th_var_fab_print_design_id_fk');
            $table->string('name', 255);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('threadorders');
    }
}
