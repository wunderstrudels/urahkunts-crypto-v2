<?php
namespace Binance;
use Illuminate\Support\Facades\Log;
use \App\Models\Transaction;
use \App\Models\Market;
use Carbon\Carbon;

class Broker {

    private $binance = null;
    private $balances = null;

    public function __construct() {
        $this->binance = new \Binance\API(env("API_X_KEY"), env("API_X_SECRET"));
    }








    public function train($wallet) {
        /* $day = Market::markets_saved()->where("currency_id", "=", $wallet->currency->id)->where('created_at', '>=', Carbon::parse('-1440 minutes'))->latest()->get();
        
        $data = array();
        foreach($day as $minute) {
            array_push($data, $minute);

            if(sizeof($data) < 60) {
                continue;
            }


        } */

        $day = Market::markets_saved()->where("currency_id", "=", $wallet->currency->id)->where('created_at', '>=', Carbon::parse('-1440 minutes'))->latest()->get();
        $latest = (isset($day[0]) == true) ? $day[0] : null;

        foreach($wallet->scenarios as $scenario) {
            $status = $scenario->status;
            $hash = $scenario->hash;

            if(self::validate($scenario) == false) {
                Log::debug("could not validate");
                continue;
            }

            if($hash == $scenario->hash) {
                Log::debug("already trained");
                continue;
            }





            $data = array();
            foreach($day as $minute) {
                array_push($data, $minute);

                if(sizeof($data) < 60) {
                    continue;
                }

                $active = Transaction::active($scenario);
                if($active == null) {
                    \SCENARIOS\Scenario::buy($scenario, $data, $latest);
                }else {
                    \SCENARIOS\Scenario::sell($scenario, $data, $active, $latest);
                }
            }






            $scenario->save();
            if($status != $scenario->status) {
                Log::debug("Wallet: {$scenario->wallet->name} Scenario: {$scenario->id}: {$scenario->status} \n");
            }
        }
    }



    public function live($wallet, $data, $current) {
        Log::debug("live");
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

        $scenario->hash = md5(file_get_contents($buy)) . "-" . md5(file_get_contents($sell));
        return true;
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