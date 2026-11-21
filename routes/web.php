<?php

use App\Http\Controllers\StockItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::resource('stock-items', StockItemController::class);
