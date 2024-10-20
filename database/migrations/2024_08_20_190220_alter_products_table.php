<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('promo_price')->nullable()->after('price');
            $table->string('type')->nullable()->after('stock');
            $table->dateTime('start_date')->nullable()->after('status');
            $table->dateTime('end_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('promo_price');
            $table->dropColumn('type');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });
    }
}
