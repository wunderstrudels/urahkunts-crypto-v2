<?php
use App\Http\Controllers\GraphController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



Route::get('/graph/trainer/{name}', [GraphController::class, 'trainer']);