<?php
use App\Http\Controllers\GraphController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



Route::get('/graph/wallets', [GraphController::class, 'wallets']);


Route::get('/graph/{short}', [GraphController::class, 'read']);
Route::get('/graph/{short}/high-low', [GraphController::class, 'highLow']);
Route::get('/graph/bots/{wallet}', [GraphController::class, 'bots']);
Route::get('/graph/transactions/{wallet}', [GraphController::class, 'transactions']);






//Route::get('/graph/trainer/{name}', [GraphController::class, 'trainer']);