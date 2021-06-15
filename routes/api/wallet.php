<?php
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



Route::post('/wallets', [WalletController::class, 'create']);
Route::get('/wallets/{id?}', [WalletController::class, 'read']);
Route::patch('/wallets/{id}', [WalletController::class, 'update']);
Route::delete('/wallets/{id}', [WalletController::class, 'delete']);