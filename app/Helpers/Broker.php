<?php
namespace Binance;
use Illuminate\Support\Facades\Log;
use \App\Models\Transaction;
use \App\Models\Market;
use \App\Models\Wallet;
use Carbon\Carbon;

class Broker {

    private $binance = null;
    private $balances = null;

    private $wallets = array();
    private $current = array(
        "wallet" => null,
        "currency" => null,
        "bot" => null,
        "scenario" => null
    );



    public function __construct() {
        $this->binance = new \Binance\API(env("API_X_KEY"), env("API_X_SECRET"));


        $avialable = $this->balance("USDC");
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
            $status = $this->buy($current);
        }else {
            $status = $this->sell($active, $current);
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
    





    private function buy($current) {
        $buy = "scenarios/" . $this->current["scenario"]->name . "/buy.php";
        $buy = include $buy;

        $buy = $buy($current);
        if($buy !== true) {
            return $buy;
        }


        $count = $this->current["wallet"]->transactions->where("status", "=", "selling")->count();
        $usd = $this->current["wallet"]->amount / (sizeof($this->current["wallet"]->bots) - $count);

        $transaction = Transaction::create(array(
            "wallet_id" => $this->current["wallet"]->id,
            "bot_id" => $this->current["bot"]->id,
            "buy_id" => 1337,
            "buy_value" => $current->value,
            "buy_price" => $usd,
            "status" => "selling",
            "created_at" => $current->created_at,
            "updated_at" => $current->created_at
        ));

        Log::debug("Just made a transaction.");
        return "Just made a transaction.";
    }



    private function sell($active, $current) {
        $sell = "scenarios/" . $this->current["scenario"]->name . "/sell.php";
        $sell = include $sell;

        $sell = $sell($active, $current);
        if($sell !== true) {
            return $sell;
        }

        $active->sell_id = 1338;
        $active->sell_value = $current->value;
        $active->sell_price = $active->buy_price;
        $active->sold_at = $current->created_at;
        $active->status = "sold";
        $active->save();

        Log::debug("Just made a sale.");
        return "Just made a sale.";
    }














    public function balance($currency) {

        if($this->balances != null) {
            return (isset($this->balances[$currency]) == true) ? $this->balances[$currency]["available"] : null;
        }

        $this->balances = $this->binance->balances();
        if($this->balances != null) {
            return (isset($this->balances[$currency]) == true) ? $this->balances[$currency]["available"] : null;
        }

        return null;
    }   


}