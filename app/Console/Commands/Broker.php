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
        $start = microtime(true);

        $broker = new \Binance\Broker();

        $end = number_format(microtime(true) - $start, 1);
        Log::debug("Broker took {$end}s.");
        return false;
    }
}
