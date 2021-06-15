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

            $last = $currency->market->last();
            if($first == true) {
                $currency->market()->where("saved", "=", false)->where('created_at', '<=', Carbon::parse('-1440 minutes'))->delete();

                $currency->market()->create(array(
                    "currency_id" => $currency->id,
                    "value" => $value,
                    "value_difference" => ($last != null) ? \Helpers::number((double)$value - (double)$last->value) : 0,
                    "percent_difference" => ($last != null) ? \Helpers::number(\Math::percentageFrom((double)$last->value, (double)$value)) : 0,
                    "saved" => true
                ));
            }else {
                $currency->market()->create(array(
                    "currency_id" => $currency->id,
                    "value" => $value,
                    "value_difference" => ($last != null) ? \Helpers::number((double)$value - (double)$last->value) : 0,
                    "percent_difference" => ($last != null) ? \Helpers::number(\Math::percentageFrom((double)$last->value, (double)$value)) : 0
                ));
            }




            



            // High / low
            $current = Table::high_low($currency->id)->whereDate('created_at', '>=', Carbon::now()->startOfDay())->whereDate('created_at', '<=', Carbon::now()->endOfDay())->first();
            $time = Carbon::now();

            if($current == null) {
                Table::high_low($currency->id)->insert(array(
                    "currency_id" => $currency->id,
                    "high" => $value,
                    "low" => $value,
                    "value_difference" => 0,
                    "percent_difference" => 0,
                    "created_at" => $time,
                    "updated_at" => $time
                ));
            } else {
                Table::high_low($currency->id)->where("id", "=", $current->id)->update(array(
                    "high" => ($value > $current->high) ? $value : $current->high,
                    "low" => ($value < $current->low) ? $value : $current->low
                ));
            }
        }
    }
}
