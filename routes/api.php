<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
require_once "api/graph.php";

Route::middleware('auth:api')->group(function () {
    require_once "api/currency.php";
    require_once "api/wallet.php";
    require_once "api/bot.php";
    require_once "api/scenario.php";


    

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

