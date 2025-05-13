<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ProductController;
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'login']);
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/uploads', [UploadController::class, 'index']);
    Route::post('/uploads', [UploadController::class, 'store']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/clear-product-cache', [ProductController::class, 'clearCache']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
});