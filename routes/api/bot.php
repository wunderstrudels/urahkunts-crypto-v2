<?php
use App\Http\Controllers\BotController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



Route::post('/bots', [BotController::class, 'create']);
Route::get('/bots/{id?}', [BotController::class, 'read']);
Route::patch('/bots/{id}', [BotController::class, 'update']);
Route::delete('/bots/{id}', [BotController::class, 'delete']);