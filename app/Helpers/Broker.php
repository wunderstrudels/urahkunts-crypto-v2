<?php
namespace Binance;
use Illuminate\Support\Facades\Log;
use \App\Models\Transaction;
use \App\Models\Currency;
use \App\Models\Market;
use \App\Models\Wallet;
use Carbon\Carbon;

class Broker {

    private $binance = null;
    
    private $account = array();
    private $commissions = array();

    private $wallets = array();
    private $current = array(
        "wallet" => null,
        "currency" => null,
        "bot" => null,
        "scenario" => null
    );



    public function __construct() {
        $this->binance = new \Binance\API(env("API_X_KEY"), env("API_X_SECRET"));


        $this->account = $this->binance->account();
        $this->commissions["taker"] = $this->account["takerCommission"] / 10000;
        $this->commissions["maker"] = $this->account["makerCommission"] / 10000;

        $balances = array();
        for($i = 0; $i < sizeof($this->account["balances"]); $i++) {
            if($this->account["balances"][$i]["asset"] != "BTC" && $this->account["balances"][$i]["asset"] != "USDC" && $this->account["balances"][$i]["asset"] != "BNB") {
                continue;
            }

            $asset = $this->account["balances"][$i]["asset"];
            $free = $this->account["balances"][$i]["free"];
            $balances[$asset] = $free;
        }
        $this->account["balances"] = $balances;


        $bnb = Currency::where("short", "=", "bnb")->first();
        $market = $bnb->market()->latest()->first();
        $usd = number_format($this->account["balances"]["BNB"] * $market->value, 2);
        if($usd < 4) {
            $buy_amount = number_format(11 / $market->value, 4);
            $buy_price = number_format($market->value, 8);

            $this->binance->buy("BNBUSDC", $buy_amount, $buy_price);
            Log::debug("Bought BNB for commissions.");
        }
        


        $avialable = $this->account["balances"]["USDC"];
        if($avialable == null) {
            return false;
        }

        $this->wallets = Wallet::where('status','=','active')->get();
        if(sizeof($this->wallets) == 0) {
            return false;
        }
        $split = $avialable / sizeof($this->wallets);




        foreach($this->wallets as $wallet) {
            $this->current["wallet"] = $wallet;
            $this->current["wallet"]->amount = $split;
            $this->current["currency"] = $wallet->currency;

            $this->invest($this->current["wallet"]);
            $this->current["wallet"]->save();
        }
    }

    private function invest($wallet) {
        for($i = 0; $i < sizeof($wallet->bots); $i++) {
            $this->current["bot"] = $wallet->bots[$i];
            $this->current["scenario"] = $this->current["bot"]->scenario;

            if(self::validate($this->current["scenario"]) == false) {
                $this->current["bot"]->status = "Could not validate scenario files.";
                $this->current["bot"]->save();
                continue;
            }

            $last = ($i != 0) ? $wallet->bots[$i - 1] : null;
            if($last == null) {
                $this->run();
            }else {
                $active = $last->active();

                if($active == null) {
                    $this->current["bot"]->status = "Last scenario havent bought yet.";
                }else {
                    $time = (isset($wallet->timeout) != false) ? $wallet->timeout : 30;
                    if(Carbon::parse($active->created_at)->diffInMinutes(Carbon::now()) < $time) {
                        $time = $time - Carbon::parse($active->created_at)->diffInMinutes(Carbon::now());
                        $this->current["bot"]->status = "Waiting " . $time . " minutes to buy.";
                    }else {
                        $this->run();
                    }
                }
            }
            $this->current["bot"]->save();
        }
    }

    private function run() {

        $current = $this->current["wallet"]->currency->market()->latest()->first();
        $active = $this->current["bot"]->active();
        $status = "";

        if($active == null) {
            $status = $this->buying($current);
        }else {
            if($active->status == "confirm_buy") {
                $status = $this->confirm_buy($active);
            }else if($active->status == "confirm_sell") {
                $status = $this->confirm_sell($active);
            }else if($active->status == "selling") {
                $status = $this->selling($active, $current);
            }
        }
        
        $this->current["bot"]->status = $status;
    }



    private static function validate($scenario = null) {

        // Buy.
        $buy = "scenarios/"  . $scenario->name . "/buy.php";
        if(file_exists($buy) == false) {
            Log::debug("Could not find the scenario 'buy' file for: " . $scenario->name);
            return false;
        }

        // Sell.
        $sell = "scenarios/"  . $scenario->name . "/sell.php";
        if(file_exists($sell) == false) {
            Log::debug("Could not find the scenario 'sell' file for: " . $scenario->name);
            return false;
        }

        return true;
    }
    





    private function buying($current) {
        $buy = "scenarios/" . $this->current["scenario"]->name . "/buy.php";
        $buy = include $buy;

        $buy = $buy($current);
        if($buy !== true) {
            return $buy;
        }


        $count = $this->current["wallet"]->transactions->where("status", "=", "selling")->count();
        $usd = $this->current["wallet"]->amount / (sizeof($this->current["wallet"]->bots) - $count);

        if($usd < 15) {
            return "Cannot afford to buy. ${$usd} left in wallet.";
        }

        $id = $this->buy($usd, $current->value);
        if($id == null) {
            return "Could not buy.";
        }

        $transaction = Transaction::create(array(
            "wallet_id" => $this->current["wallet"]->id,
            "bot_id" => $this->current["bot"]->id,
            "buy_id" => $id,
            "status" => "confirm_buy",
            "created_at" => $current->created_at,
            "updated_at" => $current->created_at
        ));

        Log::debug("Just made a transaction.");
        return "Just made a transaction.";
    }



    private function selling($active, $current) {
        $sell = "scenarios/" . $this->current["scenario"]->name . "/sell.php";
        $sell = include $sell;

        $sell = $sell($active, $current);
        if($sell !== true) {
            return $sell;
        }

        $id = $this->sell($active->amount, $current->value);
        if($id == null) {
            return "Could not sell.";
        }

        $active->sell_id = $id;
        $active->status = "confirm_sell";
        $active->sold_at = $current->created_at;
        $active->save();

        Log::debug("Just made a sale.");
        return "Just made a sale.";
    }















    // BUY FUNCTIONS
    private function buy($amount, $price) {
        $buy_amount = number_format($amount / $price, 6, '.', '');
        $buy_price = number_format($price, 8, '.', '');
        $buy_currency = $this->current["wallet"]->currency->name;

        $response = $this->binance->buy($buy_currency, $buy_amount, $buy_price);

        if($response == null || isset($response["orderId"]) == false) {
            return null;
        }

        return $response["orderId"];
    }

    private function confirm_buy($active) {
        $buy_currency = $this->current["wallet"]->currency->name;

        $response = $this->binance->orderStatus($buy_currency, $active->buy_id);
        if($response == null || isset($response["orderId"]) == false) {
            return "Could not confirm buy.";
        }

        if($response["status"] != "FILLED") {
            $age = Carbon::parse($active->created_at)->diffInMinutes(Carbon::now());
            if($age >= 3) {
                return $this->cancel($active);
            }

            return "Order not yet filled.";
        }


        $active->buy_price = $response["cummulativeQuoteQty"];
        $active->amount =  number_format($response["executedQty"], 8);
        $active->buy_value = $response["price"];
        $active->status = "selling";
        $active->save();


        
        return "Buy has been confirmed.";
    }





    // SELL FUNCTIONS
    private function sell($amount, $price) {
        $sell_amount = number_format($amount, 6, '.', '');
        $sell_price = number_format($price, 8, '.', '');
        $sell_currency = $this->current["wallet"]->currency->name;

        $response = $this->binance->sell($sell_currency, $sell_amount, $sell_price);
        if($response == null || isset($response["orderId"]) == false) {
            return null;
        }

        return $response["orderId"];
    }

    private function confirm_sell($active) {
        $currency = $this->current["wallet"]->currency->name;
        
        $response = $this->binance->orderStatus($currency, $active->sell_id);
        if($response == null || isset($response["orderId"]) == false) {
            return "Could not confirm sale.";
        }

        if($response["status"] != "FILLED") {
            $age = Carbon::parse($active->sold_at)->diffInMinutes(Carbon::now());
            if($age >= 3) {
                return $this->cancel($active);
            }

            return "Order not yet filled.";
        }

        $active->sell_price = $response["cummulativeQuoteQty"];
        $active->sell_value = $response["price"];
        $active->status = "sold";
        $active->save();

        return "Sale has been confirmed.";
    }






    // CANCEL
    private function cancel($active) {
        $currency = $this->current["wallet"]->currency->name;

        $response = null;
        if($active->status == "confirm_buy") {
            $response = $this->binance->cancel($currency, $active->buy_id);
        }else if($active->status == "confirm_sell") {
            $response = $this->binance->cancel($currency, $active->sell_id);
        }

        if($response == null || isset($response["msg"]) == true) {
            return "Error: " . $response["msg"];
        }


        if($active->status == "confirm_buy") {
            $active->delete();
        }else if($active->status == "confirm_sell") {
            $active->sell_id = null;
            $active->status = "selling";
            $active->save();
        }

        return "Order cancled.";
    }

}