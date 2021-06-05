<?php
namespace SCENARIOS;
use Illuminate\Support\Facades\Log;

class Scenario {

    public static function buy($scenario) {
        $buy = "scenarios/"  . $scenario->name . "/buy.php";
        $buy = require_once $buy;

        $buy();
        Log::debug("buy");
    }



    public static function sell($scenario) {
        $sell = "scenarios/"  . $scenario->name . "/sell.php";
        $sell = require_once $sell;

        $sell();
        Log::debug("sell");
    }
}
