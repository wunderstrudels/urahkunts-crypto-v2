<?php
use App\Http\Controllers\GraphController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



Route::get('/graph/{short}', [GraphController::class, 'read']);
Route::get('/graph/{short}/high-low', [GraphController::class, 'highLow']);



Route::get('/graph/trainer/{name}', [GraphController::class, 'trainer']);