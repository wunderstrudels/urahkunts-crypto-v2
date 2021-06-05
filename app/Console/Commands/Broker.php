<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use \App\Models\Market;
use App\Models\Wallet;
use Carbon\Carbon;

class Broker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:broker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {

        $broker = new \Binance\Broker();
        $usdc_avialable = $broker->balance("USDC");
        if($usdc_avialable == null) {
            return false;
        }

        $count = Wallet::where('status','=','live')->count();
        $wallet_split = ($count != 0) ? $usdc_avialable / $count : 0;



        
        foreach(Wallet::all() as $wallet) {
            if($wallet->status == "training") {
                $broker->train($wallet);
            }else if($wallet->status == "live") {

                $m30 = Market::where("currency_id", "=", $wallet->currency->id)->latest()->get();
                $m60 = Market::markets_saved()->where("currency_id", "=", $wallet->currency->id)->where('created_at', '>=', Carbon::parse('-60 minutes'))->latest()->get();
                $data = array(
                    "30m" => ($m30 != null) ? $m30 : [],
                    "60m" => ($m60 != null) ? $m60 : []
                );
                $current = (isset($m30[0]) == true) ? $m30[0] : null;

                
                $broker->live($wallet, $data, $current);
            }
        }
        return false;
    }
}
