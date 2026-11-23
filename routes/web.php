<?php

use App\Http\Controllers\StockItemController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

// Маршрути автентифікації для гостей
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);
    Route::get('/register', [WebAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register']);
});

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Публічний маршрут перегляду списку товарів
Route::resource('stock-items', StockItemController::class)->only(['index']);

// Захищені маршрути зміни даних (гостей перенаправить на /login)
Route::middleware('auth')->group(function () {
    Route::resource('stock-items', StockItemController::class)->except(['index', 'show']);
});