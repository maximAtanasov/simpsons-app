<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

//Protected endpoints
Route::middleware('auth:sanctum')->get('/quotes', [QuoteController::class, 'fetchQuotes']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
