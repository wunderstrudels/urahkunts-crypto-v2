<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Scenario;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;
class Trainer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:trainer';

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
    public function handle()
    {   
        
        foreach(Scenario::where("status", "=", null)->get() as $scenario) {
            $start = microtime(true);
            
            $scenario->status = "training";
            $scenario->save();

            $trained = new \Binance\Trainer($scenario);

            $scenario->status = "trained";
            $scenario->save();

            $end = number_format(microtime(true) - $start, 1);
            Log::debug("Trained scenario: {$scenario->name} in {$end}s.");
        }
        return false;
    }
}
