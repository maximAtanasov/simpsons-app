<?php

use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

Route::get('/quote', [QuoteController::class, 'index'])->name('api.quote');

