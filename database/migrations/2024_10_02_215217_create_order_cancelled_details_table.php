<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderCancelledDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_cancelled_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id'); 
            $table->unsignedBigInteger('product_id'); 
            $table->unsignedBigInteger('seller_id');
            $table->integer('price'); 
            $table->integer('qty');
            $table->string('weight'); 
            $table->string('shipping_courier'); 
            $table->integer('shipping_cost');
            $table->string('shipping_service');
            $table->string('status');
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
        Schema::dropIfExists('order_cancelled_details');
    }
}
