<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Currency;
use \App\Models\Wallet;
use \App\Models\Market;
use \App\Models\Bot;
use Carbon\Carbon;

class GraphController extends Controller {

    public function __construct() {
        $this->middleware('auth:api', ['except' => [
            "read",
            "wallets",
            "bots",
            "transactions"
        ]]);
    }



    public function read(Request $request, $id) {
        $output = array(
            "labels" => array(),
            "values" => array()
        );


        
        $currency = Currency::where("id", "=", $id)->orWhere("short", "=", $id)->first();
        if($currency == null) {
            return response()->json(array("error" => "Could not find currency: " . $id), 404);
        }

        if(isset($request->from) == false && isset($request->to) == false) {
            $market = $currency->market()->where("saved", "=", true)->where('created_at', '>=', Carbon::parse('-1440 minutes'))->where('created_at', '<=', Carbon::parse('-30 minutes'))->get();
            foreach($market as $item) {
                
                array_push($output["values"], $item->value);

                // Labels
                $timestamp = strtotime($item->created_at);
                $time = date('H:i', $timestamp);
                array_push($output["labels"], $time);
            }

            $market = $currency->market()->where('created_at', '>=', Carbon::parse('-30 minutes'))->get();
            foreach($market as $item) {
                
                array_push($output["values"], $item->value);

                // Labels
                $timestamp = strtotime($item->created_at);
                $time = date('H:i:s', $timestamp);
                array_push($output["labels"], $time);
            }
        }

        if(isset($request->from) == true && isset($request->to) == false) {
            $market = $currency->market()->where('created_at', '>=', Carbon::parse($request->from))->get();
            foreach($market as $item) {
                
                array_push($output["values"], $item->value);

                // Labels
                $timestamp = strtotime($item->created_at);
                $time = date('H:i:s', $timestamp);
                array_push($output["labels"], $time);
            }
        }

        if(isset($request->from) == true && isset($request->to) == true) {

        }


        //dd($output);




       /*  $data = array();
        if(isset($request->from) == true && isset($request->to) == true) {
            $data = Market::market_saved($currency->id)->where('created_at', '>=', Carbon::parse($request->from))->where('created_at', '<=', Carbon::parse($request->to))->get();
        }else {
            $data = Market::market_saved($currency->id)->where('created_at', '>=', Carbon::parse('-1440 minutes'))->get();
        }

        foreach($data as $minute) {
            
            array_push($output["values"], $minute->value);

            // Labels
            $timestamp = strtotime($minute->created_at);
            $time = date('H:i:s', $timestamp);
            array_push($output["labels"], $time);
        }
 */


        return response()->json(array("graph" => $output));
    }
    
    public function wallets(Request $request, $id = null) {
        return response()->json(array(
            "wallets" => Wallet::select("id", "name", "currency_id")->get()
        ), 200);
    }
    
    public function transactions(Request $request, $id) {
        $wallet = Wallet::where("id", "=", $id)->orWhere("name", "=", $id)->first();


        $output = array(
            "profits" => 0,
            "points" => array(),
            "lines" => array()
        );

        
        $points = array();
        foreach($wallet->bots as $bot) {
            foreach($bot->transactions->where('created_at', '>=', Carbon::parse('-1440 minutes'))->where('created_at', '<=', Carbon::parse('-30 minutes')) as $transaction) {
                // Bought at
                $timestamp = strtotime($transaction->created_at);
                $bought = date('H:i', $timestamp);
                $color = $bot->color;

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

                    $output["profits"] = $output["profits"] + $transaction->sell_price - $transaction->buy_price;
                }
            }

            foreach($bot->transactions->where('created_at', '>=', Carbon::parse('-30 minutes')) as $transaction) {
                // Bought at
                $timestamp = strtotime($transaction->created_at);
                $bought = date('H:i', $timestamp);
                $color = $bot->color;

                array_push($points, array(
                    "buy_time" => $bought,
                    "buy_value" => $transaction->buy_value,
                    "label" => "Buy",
                    "color" => $color
                ));

                if($transaction->status == "sold") {
                    // Sold at
                    $timestamp = strtotime($transaction->sold_at);
                    $sold = date('H:i:s', $timestamp);

                    array_push($points, array(
                        "sell_time" => $sold,
                        "sell_value" => $transaction->sell_value,
                        "label" => "Sold",
                        "color" => $color
                    ));

                    $output["profits"] = $output["profits"] + $transaction->sell_price - $transaction->buy_price;
                }
            }
        }
        $output["points"] = $points;
        $output["profits"] = number_format($output["profits"], 2);

        foreach($wallet->transactions->where("status", "=", "selling")->where('created_at', '<=', Carbon::parse('-1440 minutes')) as $transaction) {
            // Bought at
            $timestamp = strtotime($transaction->created_at);
            $bought = date('H:i', $timestamp);
            $color = $bot->color;
            
            array_push($output["lines"], array(
                "buy_time" => $bought,
                "buy_value" => $transaction->buy_value,
                "label" => "Buy",
                "color" => $color
            ));
        }

        return response()->json(array("transactions" => $output));
    }


    
    public function bots(Request $request, $id) {
        $wallet = Wallet::where("id", "=", $id)->orWhere("name", "=", $id)->first();


        return response()->json($wallet->bots()->select("name", "status")->get(), 200);
    }


    public function highLow(Request $request, $short) {

        $currency = Currency::where("short", "=", $short)->first();
        if($currency == null) {
            return response()->json(array("error" => "Could not find currency: " . $short), 404);
        }

        
        $output = array(
            "high" => array(
                "values" => array()
            ),
            "low" => array(
                "values" => array()
            ),
            "labels" => array()
        );
        for($i = 0; $i < 30; $i++) {
            //$time = (isset($request->from) == true) ? Carbon::parse($request->from) : Carbon::now();
            //$time = $time->parse('-' . $i . ' days');
            $time = Carbon::now();
            $time = $time->parse('-' . $i . ' days');
            $high = $currency->market->market_saved()->whereDate('created_at', '>=', $time->startOfDay())->whereDate('created_at', '<=', $time->endOfDay())->max("value");
            $low = $currency->market->market_saved()->whereDate('created_at', '>=', $time->startOfDay())->whereDate('created_at', '<=', $time->endOfDay())->min("value");
            
            if($low == null || $high == null) {
                continue;
            }

            array_push($output["high"]["values"], $high);
            array_push($output["low"]["values"], $low);

            $timestamp = strtotime($time);
            $time = date('H:i:s', $timestamp);
            array_push($output["labels"], $time);
        }

        
        return response()->json($output, 200);
    }






    public function trainer(Request $request, $name) {
        $wallet = Wallet::where("status", "=", "training")->first();
        $scenario = $wallet->scenarios->where("name", "=", $name)->first();
        $start = new Carbon($scenario->created_at, 'Europe/Copenhagen');
        
        
        $day = $wallet->currency->market->market_saved()->where('created_at', '>=', $start->parse('-720 minutes'))->get();


        $output = array(
            "labels" => array(),
            "values" => array()
        );
        foreach($day as $minute) {
            
            array_push($output["values"], $minute->value);

            // Labels
            $timestamp = strtotime($minute->created_at);
            $time = date('H:i:s', $timestamp);
            array_push($output["labels"], $time);
        }





        $transactions = $wallet->transactions->where('created_at', '>=', $start->parse('-720 minutes'));
        $points = array();
        foreach($transactions as $transaction) {
            // Bought at
            $timestamp = strtotime($transaction->created_at);
            $bought = date('H:i:s', $timestamp);
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
                $sold = date('H:i:s', $timestamp);

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
