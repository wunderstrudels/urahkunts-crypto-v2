<?php
use App\Http\Controllers\ScenarioController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



Route::post('/scenarios', [ScenarioController::class, 'create']);
Route::get('/scenarios/{id?}', [ScenarioController::class, 'read']);
Route::patch('/scenarios/{id}', [ScenarioController::class, 'update']);
Route::delete('/scenarios/{id}', [ScenarioController::class, 'delete']);

Route::get('/scenarios/{id}/graph', [ScenarioController::class, 'graph']);
Route::post('/scenarios/{id}/code', [ScenarioController::class, 'code']);