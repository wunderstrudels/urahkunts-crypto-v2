<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Wallet;

class WalletController extends Controller {
    // CREATE
    public function create(Request $request) {
        
        $api = new \Binance\API(env("API_X_KEY"), env("API_X_SECRET"));
        //$price = number_format($api->price("BTCUSDC"), 8, '.', '');

        /* $amount = 15;
        $buy_amount = number_format($amount / $api->price("BTCUSDC"), 6, '.', '');
        $price = number_format($api->price("BTCUSDC"), 8, '.', '');
        */

        //$response = $api->buy("BTCUSDC", $buy_amount, $price); 
        //$response = $api->sell("BTCUSDC", 0.000375, $price); 
        //$response = $api->orderStatus("BTCUSDC", "492547012");
        /* $response = $api->account();
        $balances = array();
        for($i = 0; $i < sizeof($response["balances"]); $i++) {
            if($response["balances"][$i]["asset"] != "BTC" && $response["balances"][$i]["asset"] != "USDC") {
                continue;
            }

            $asset = $response["balances"][$i]["asset"];
            $free = $response["balances"][$i]["free"];
            $balances[$asset] = $free;
        }
        $response["balances"] = $balances; */

        /* $price = number_format($api->price("BTCUSDC"), 8, '.', '');
        $response = $api->sell("BTCUSDC", 0.00037500, $price);  */

        /* $buy_amount = number_format(1 / $api->price("BNBUSDC"), 6, '.', '');
        $price = number_format($api->price("BNBUSDC"), 8, '.', '');
        $response = $api->buy("BNBUSDC", $buy_amount, $price); 

        dd($response); */

        //$buy_amount = number_format(10 / $api->price("BTCUSDC"), 6, '.', '');
        /* $price = number_format($api->price("BTCUSDC"), 8, '.', '');
        $response = $api->sell("BTCUSDC", 0.000312, $price);

        dd($response); */



        //$response = $api->exchangeInfo()["symbols"]["ANKRBNB"];
        /* $response = number_format($api->price("ANKRUSDT"), 8, '.', '');
        dd($response); */

        return response()->json(array(
            "success" => true
        ));
    }

    // READ
    public function read(Request $request, $id = null) {
        
        if($id == null) {
            return response()->json(Wallet::all(), 200);
        }

        $wallet = Wallet::where("id", "=", $id)->orWhere("name", "=", $id)->first();

        return response()->json($wallet, 200);
    }

    // UPDATE
    public function update(Request $request) {
        
        return response()->json(array(
            "success" => true
        ));
    }

    // DELETE
    public function delete(Request $request) {
        
        return response()->json(array(
            "success" => true
        ));
    }
}
