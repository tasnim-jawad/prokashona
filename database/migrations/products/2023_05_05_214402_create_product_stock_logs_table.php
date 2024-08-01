<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStockLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("product_id")->unsigned()->nullable();
            $table->bigInteger("qty")->unsigned()->nullable();
            $table->string("type",15)->comment("sale, production");
            $table->bigInteger("productions_id")->unsigned()->nullable();
            $table->bigInteger("order_id")->unsigned()->nullable();
            $table->bigInteger("product_return_id")->unsigned()->nullable();

            $table->date("date")->nullable();
            $table->string("mr_no",45)->nullable();
            $table->string("supplier_name",100)->nullable();
            $table->string("order_id",100)->nullable();
            $table->string("binder_name",100)->nullable();

            $table->bigInteger('creator')->unsigned()->nullable();
            $table->string('slug', 50)->nullable();
            $table->tinyInteger('status')->unsigned()->default(1);

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
        Schema::dropIfExists('product_stock_logs');
    }
}
