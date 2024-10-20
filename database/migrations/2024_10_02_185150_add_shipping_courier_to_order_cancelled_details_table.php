<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingCourierToOrderCancelledDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_cancelled_details', function (Blueprint $table) {
            $table->string('shipping_courier')->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_cancelled_details', function (Blueprint $table) {
            $table->dropColumn('shipping_courier');
        });
    }
}
