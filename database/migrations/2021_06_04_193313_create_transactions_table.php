<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();


            // Relations
            $table->unsignedBigInteger('wallet_id');
            $table->foreign('wallet_id')->references('id')->on('wallets');
            $table->unsignedBigInteger('bot_id');
            $table->foreign('bot_id')->references('id')->on('bots');


            // Default
            $table->string('amount')->default("");


            // Buy
            $table->string('buy_id')->nullable();
            $table->string('buy_value')->default("");
            $table->string('buy_price')->default("");


            // Sell
            $table->string('sell_id')->nullable();
            $table->string('sell_value')->default("");
            $table->string('sell_price')->default("");
            $table->timestamp('sold_at')->nullable();

            
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
        Schema::dropIfExists('transactions');
    }
}
