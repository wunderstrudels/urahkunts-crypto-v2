<?php

namespace Database\Seeders;


use Database\Seeders\ScenarioSeeder;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\WalletSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            CurrencySeeder::class,
            ScenarioSeeder::class,
            WalletSeeder::class
        ]);
    }
}
