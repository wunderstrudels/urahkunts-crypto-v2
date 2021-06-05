<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $jeppe = User::create(array(
            "name" => "Jeppe",
            "username" => "jeppe",
            "email" => "jeppekristoffersen@hotmail.com",
            "phone" => "88888888",
            "password" => Hash::make("jeppe1234")
        ));

        $jonas = User::create(array(
            "name" => "Jonas",
            "username" => "jonas",
            "email" => "jonaskristoffersen@hotmail.com",
            "phone" => "88888888",
            "password" => Hash::make("jonas1234")
        ));
    }
}