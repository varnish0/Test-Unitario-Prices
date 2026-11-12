<?php

use App\Http\Controllers\PriceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/prices', [PriceController::class, 'index'])->name('prices.index');
Route::post('/prices', [PriceController::class, 'calculate'])->name('prices.calculate');
