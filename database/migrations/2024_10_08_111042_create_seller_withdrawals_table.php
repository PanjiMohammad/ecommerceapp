<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id');  // Same type as in 'sellers' table
            $table->unsignedBigInteger('bank_account_id');  // Foreign key to 'seller_bank_accounts'
            $table->decimal('amount', 15, 2);
            $table->string('status')->nullable();
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
        Schema::dropIfExists('seller_withdrawals');
    }
}
