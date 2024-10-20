<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderCancelledTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_cancelled', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice')->unique();
            $table->string('customer_id');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_address');
            $table->unsignedBigInteger('district_id'); 
            $table->integer('subtotal');
            $table->integer('cost');
            $table->integer('packaging_cost');
            $table->integer('service_cost');
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
        Schema::dropIfExists('order_cancelled');
    }
}
