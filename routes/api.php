<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\RegisterController;
use App\Http\Controllers\v1\SessionsController;
use App\Http\Controllers\v1\ItemController;
use App\Http\Controllers\v1\CommentController;
use App\Http\Controllers\v1\BidController;
use App\Http\Controllers\v1\WishlistController;
use App\Http\Controllers\v1\ProfileController;
use App\Http\Controllers\v1\ReportController;

Route::prefix('v1')->group(function () {

    Route::post('/register', [RegisterController::class, 'store']);
    Route::post('/login', [SessionsController::class, 'store']);
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/categories', [ItemController::class, 'categories']);
    Route::get('/items/{item}', [ItemController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/logout', [SessionsController::class, 'destroy']);
        Route::post('/items', [ItemController::class, 'store']);
        Route::patch('/items/{item}', [ItemController::class, 'update']);
        Route::delete('/items/{item}', [ItemController::class, 'destroy']);
        Route::post('/items/{item}/comments', [CommentController::class, 'store']);
        Route::post('/items/{item}/bids', [BidController::class, 'store']);
        Route::post('/items/{item}/wishlist', [WishlistController::class, 'toggle']);
        Route::post('/items/{item}/reports', [ReportController::Class, 'store']);
        
        // User Profile Routes
        Route::get('/profile/items', [ProfileController::Class, 'items']);
        Route::get('/profile/bids', [ProfileController::Class, 'bids']);
        Route::get('/profile/wishlist', [ProfileController::Class, 'wishlist']);
    });
});