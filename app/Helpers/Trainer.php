<?php
namespace Binance;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use \App\Models\Currency;
use \App\Models\Market;
use Carbon\Carbon;


class Trainer {
    private $latest = null;
    private $data = array();
    private $wallet = 100;

    
    private $active = null;
    public $transactions = array();


    public function __construct($scenario) {

        // Validating.
        if(file_exists("scenarios/" . $scenario->name) == false) {
            Log::debug("Could not find folder 'scenarios/{$scenario->name}'.");
            return false;
        }

        if(file_exists("scenarios/" . $scenario->name . "/buy.php") == false) {
            Log::debug("Could not find file 'scenarios/{$scenario->name}/buy.php'.");
            return false;
        }

        if(file_exists("scenarios/" . $scenario->name . "/sell.php") == false) {
            Log::debug("Could not find file 'scenarios/{$scenario->name}/sell.php'.");
            return false;
        }



        // Start the training.
        $btc = Currency::where("short", "=", "btc")->first();
        $this->data = $btc->market()->where('created_at', '>=', Carbon::parse('-1440 minutes'))->get();
        $this->latest = (isset($this->data[0]) == true) ? $this->data[0] : null;


        foreach($this->data as $minute) {
            $this->latest = $minute;

            if($this->active == null) {
                $this->buy($scenario, $this->latest);
            }else {
                $this->sell($scenario, $this->active, $this->latest);
            }
        }

        $this->save($scenario);
    }


    private function buy($scenario, $latest) {
        $buy = "scenarios/" . $scenario->name . "/buy.php";
        $buy = include $buy;

        if($buy($latest) == false) {
            return false;
        }

        $this->active = array(
            "id" => sizeof($this->transactions),
            "wallet_id" => 1337,
            "bot_id" => 1337,
            "amount" => (isset($latest) != false) ? ($this->wallet / $latest->value) : 0,
            "buy_id" => 1337,
            "buy_value" => $latest->value,
            "buy_price" => $this->wallet,
            "sell_id" => null,
            "sell_value" => 0,
            "sell_price" => 0,
            "sold_at" => null,
            "status" => "selling",
            "created_at" => $latest->created_at,
            "updated_at" => $latest->created_at,
        );



        array_push($this->transactions, $this->active);
    }



    private function sell($scenario, $active, $latest) {
        $sell = "scenarios/" . $scenario->name . "/sell.php";
        $sell = include $sell;

        if($sell($active, $latest) == false) {
            return false;
        }

        $id = $active["id"];
        $this->transactions[$id]["sell_id"] = 1337;
        $this->transactions[$id]["sell_value"] = $latest->value;
        $this->transactions[$id]["sell_price"] = (isset($latest) != false) ? ($latest->value * $this->wallet) : 0;
        $this->transactions[$id]["sold_at"] = $latest->created_at;
        $this->transactions[$id]["status"] = "sold";

        $this->active = null;
    }

    private function save($scenario) {


        // Graph
        $output = array(
            "labels" => array(),
            "values" => array()
        );
        foreach($this->data as $minute) {
            
            array_push($output["values"], $minute->value);

            // Labels
            $timestamp = strtotime($minute->created_at);
            $time = date('H:i:s', $timestamp);
            array_push($output["labels"], $time);
        }

        

        // Transactions
        $points = array();
        foreach($this->transactions as $transaction) {
            // Bought at
            $timestamp = strtotime($transaction["created_at"]);
            $bought = date('H:i:s', $timestamp);
            $color = "#000000";

            array_push($points, array(
                "buy_time" => $bought,
                "buy_value" => $transaction["buy_value"],
                "label" => "Buy",
                "color" => $color
            ));

            if($transaction["status"] == "sold") {
                // Sold at
                $timestamp = strtotime($transaction["sold_at"]);
                $sold = date('H:i:s', $timestamp);

                array_push($points, array(
                    "sell_time" => $sold,
                    "sell_value" => $transaction["sell_value"],
                    "label" => "Sold",
                    "color" => $color
                ));
            }
        }
        $output["points"] = $points;



        file_put_contents("scenarios/" . $scenario->name . "/data.json", json_encode($output));
    }
}
