<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketsSavedOnMarketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::connection('market')->hasTable('markets_saved') == true) {
            return false;
        }

        Schema::connection('market')->create('markets_saved', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('currency_id');
            $table->string('value')->default(0);
            $table->string('value_difference')->default(0);
            $table->string('percent_difference')->default(0);
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
        Schema::connection('market')->dropIfExists('markets_saved');
    }
}
