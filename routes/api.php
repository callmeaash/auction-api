<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;

Route::middleware('guest')->group(function () {

    Route::post('/register', [RegisterController::class, 'store']);
    Route::post('/login', [SessionsController::class, 'store']);

});

Route::middleware('auth:sanctum')->group(function () {
    

    Route::delete('/logout', [SessionsController::class, 'destroy']);
});