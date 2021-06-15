<?php
use App\Http\Controllers\CurrencyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



Route::get('/currencies/{short?}', [CurrencyController::class, 'read']);