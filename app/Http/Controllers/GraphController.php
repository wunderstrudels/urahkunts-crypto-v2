<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Wallet;
use \App\Models\Market;
use Carbon\Carbon;

class GraphController extends Controller {

    public function __construct() {
        $this->middleware('auth:api', ['except' => [

        ]]);
    }








    public function trainer(Request $request, $name) {
        $wallet = Wallet::where("status", "=", "training")->first();
        $scenario = $wallet->scenarios->where("name", "=", $name)->first();
        $start = new Carbon($scenario->created_at, 'Europe/Copenhagen');
        
        
        $day = Market::markets_saved()->where("currency_id", "=", $wallet->currency->id)->where('created_at', '>=', $start->parse('-720 minutes'))->get();


        $output = array(
            "labels" => array(),
            "values" => array()
        );
        foreach($day as $minute) {
            
            array_push($output["values"], $minute->value);

            // Labels
            $timestamp = strtotime($minute->created_at);
            $time = date('H:i', $timestamp);
            array_push($output["labels"], $time);
        }





        $transactions = $wallet->transactions->where('created_at', '>=', $start->parse('-720 minutes'));
        $points = array();
        foreach($transactions as $transaction) {
            // Bought at
            $timestamp = strtotime($transaction->created_at);
            $bought = date('H:i', $timestamp);
            $color = $transaction->scenario->color;

            array_push($points, array(
                "buy_time" => $bought,
                "buy_value" => $transaction->buy_value,
                "label" => "Buy",
                "color" => $color
            ));

            if($transaction->status == "sold") {
                // Sold at
                $timestamp = strtotime($transaction->sold_at);
                $sold = date('H:i', $timestamp);

                array_push($points, array(
                    "sell_time" => $sold,
                    "sell_value" => $transaction->sell_value,
                    "label" => "Sold",
                    "color" => $color
                ));
            }
        }
        $output["points"] = $points;


        return response()->json(array("graph" => $output));
    }









}
