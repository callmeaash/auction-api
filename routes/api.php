<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\RegisterController;
use App\Http\Controllers\v1\SessionsController;
use App\Http\Controllers\v1\ItemController;

Route::prefix('v1')->group(function () {

    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{item}', [ItemController::class, 'show']);

    Route::middleware('guest')->group(function () {
        Route::post('/register', [RegisterController::class, 'store']);
        Route::post('/login', [SessionsController::class, 'store']);
        Route::get('/items/categories', [ItemController::class, 'categories']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/logout', [SessionsController::class, 'destroy']);
        Route::post('/items', [ItemController::class, 'store']);
    });
});