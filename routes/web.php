<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $api = new \Binance\API(env("API_X_KEY"), env("API_X_SECRET"));

    $prices = $api ->prices();
    if($prices == null) {
        return false;
    }

    foreach($prices as $key => $value) {
        if(substr($key,  -4) != "USDC") {
            continue;
        }


        echo $key . " : " . $value . "<br>";
    }

    //dd($data);
});
