<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Currency;
use App\Models\Wallet;


class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {


        // BTC 
        $currency = Currency::create(array(
            "name" => "BTCUSDC",
            "short" => "BTC"
        ));



        // Default
        $default = Wallet::create(array(
            "currency_id" => $currency->id,
            "name" => "trainer",
            "amount" => 100
        ));
        $default->scenarios()->create(array(
            "name" => "default",
            "color" => \Helpers::random_color()
        ));
    }
}