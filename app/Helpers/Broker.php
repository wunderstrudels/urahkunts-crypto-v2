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