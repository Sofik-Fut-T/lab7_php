<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StockItemApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/stock-items', [StockItemApiController::class, 'index']);
    Route::get('/stock-items/{stockItem}', [StockItemApiController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/stock-items', [StockItemApiController::class, 'store']);
        Route::put('/stock-items/{stockItem}', [StockItemApiController::class, 'update']);
        Route::delete('/stock-items/{stockItem}', [StockItemApiController::class, 'destroy']);
    });
});