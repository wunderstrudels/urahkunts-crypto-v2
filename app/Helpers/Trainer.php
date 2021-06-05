<?php
namespace Binance;
use Illuminate\Support\Facades\Log;
use \App\Models\Transaction;
use \App\Models\Market;
use Carbon\Carbon;


class Trainer {
    public static function run($wallet) {
        $day = Market::markets_saved()->where("currency_id", "=", $wallet->currency->id)->where('created_at', '>=', Carbon::parse('-360 minutes'))->get();
        $latest = (isset($day[0]) == true) ? $day[0] : null;

        foreach($wallet->scenarios as $scenario) {
            $status = $scenario->status;

            if($scenario->hash != "") {
                Log::debug("already trained");
                continue;
            }
            $scenario->hash = md5($scenario->name);


            if(self::validate($scenario) == false) {
                Log::debug("could not validate");
                continue;
            }

            
            Transaction::where('scenario_id', '=', $scenario->id)->delete();

            $data = array();
            foreach($day as $minute) {
                array_push($data, $minute);

                if(sizeof($data) < 60) {
                    continue;
                }
                $latest = $minute;




                $active = Transaction::active($scenario);
                if($active == null) {
                    self::buy($scenario, $data, $latest);
                }else {
                    self::sell($scenario, $data, $active, $latest);
                }
            }


            if($status != $scenario->status) {
                $size = sizeof($scenario->transactions);
                Log::debug("Wallet: {$scenario->wallet->name} Scenario: {$scenario->id}: {$size} \n");
            }
            
            $scenario->status = "";
            $scenario->save();            
        }
    }

    private static function validate($scenario = null) {

        // Buy.
        $buy = "scenarios/"  . $scenario->name . "/buy.php";
        if(file_exists($buy) == false) {
            $scenario->status = "Could not find the scenario 'buy' file.";
            return false;
        }

        // Sell.
        $sell = "scenarios/"  . $scenario->name . "/sell.php";
        if(file_exists($sell) == false) {
            $scenario->status = "Could not find the scenario 'sell' file.";
            return false;
        }

        return true;
    }



    private static function buy($scenario, $data, $latest) {
        $buy = "scenarios/"  . $scenario->name . "/buy.php";
        $buy = include $buy;

        if($buy($data, $latest) != true) {
            return false;
        }



        $scenario->wallet->amount = 100;

        if($scenario->wallet->amount < 10) {
            $scenario->status = " - Wallet is under minimum buy amount: {$scenario->wallet->amount}.";
            return false;
        }

        $transaction = Transaction::create(array(
            "wallet_id" => $scenario->wallet->id,
            "scenario_id" => $scenario->id,
            "amount" => (isset($latest) != false) ? ($scenario->wallet->amount / $latest->value) : 0,
            "buy_price" => $scenario->wallet->amount,
            "buy_value" => (isset($latest) != false) ? $latest->value : 0,
            "status" => "selling"
        ));
        $transaction->created_at = $latest->created_at;
        $transaction->save();

        $scenario->wallet->amount = $scenario->wallet->amount - $transaction->buy_price;
        $scenario->wallet->save();

        $scenario->status = " - Just made a new transaction.";
    }

    private static function sell($scenario, $data, $active, $latest) {
        $sell = "scenarios/"  . $scenario->name . "/sell.php";
        $sell = include $sell;

        if($sell($data, $active, $latest) != true) {
            return false;
        }

        $active->sell_price = (isset($latest) != false) ? ($latest->value * $active->amount) : 0;
        $active->sell_value = (isset($latest) != false) ? $latest->value : 0;
        $active->sold_at = $latest->created_at;
        $active->status = "sold";
        $active->save();

        
        $scenario->wallet->amount = $scenario->wallet->amount + $active->sell_price;
        $scenario->wallet->save();

        $scenario->status = " - Just made a new sale.";
    }
}
