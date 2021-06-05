<?php
namespace Binance;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use \App\Models\Market as Table;
use \App\Models\Currency;
use Carbon\Carbon;

class Market {
    private $binance = null;

    public function __construct() {
        $this->binance = new \Binance\API(env("API_X_KEY"), env("API_X_SECRET"));
    }

    public function fetch($first = false) {
        
        $data = $this->binance->prices();
        foreach($data as $key => $value) {

            if(substr($key,  -4) != "USDC") {
                continue;
            }

            $currency = Currency::updateOrCreate(array("name" => $key), array(
                "name" => $key,
                "short" => str_replace("USDC", "", $key)
            ));

            $last = Table::where("currency_id", "=", $currency->id)->latest()->first();
            Table::create(array(
                "currency_id" => $currency->id,
                "value" => $value,
                "value_difference" => ($last != null) ? $last->value - $value : 0,
                "percent_difference" => ($last != null) ? \Math::percentageFrom($last->value, $value) : 0
            ));




            
            if($first == true) {
                Table::where('created_at', '<=', Carbon::parse('-30 minutes'))->delete();
                DB::connection("market")->table('markets_saved')->insert(array(
                    "currency_id" => $currency->id,
                    "value" => $value,
                    "value_difference" => ($last != null) ? $last->value - $value : 0,
                    "percent_difference" => ($last != null) ? \Math::percentageFrom($last->value, $value) : 0,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()
                ));
            }
        }
    }
}
