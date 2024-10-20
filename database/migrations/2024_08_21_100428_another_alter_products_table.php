<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AnotherAlterProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('storage_instructions')->nullable()->after('type');
            $table->string('storage_period')->nullable()->after('storage_instructions');
            $table->string('units')->nullable()->after('storage_period');
            $table->string('packaging')->nullable()->after('units');
            $table->string('serving_suggestions')->nullable()->after('packaging');
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
            $table->dropColumn('storage_instructions');
            $table->dropColumn('storage_period');
            $table->dropColumn('units');
            $table->dropColumn('packaging');
            $table->dropColumn('serving_suggestions');
        });
    }
}
