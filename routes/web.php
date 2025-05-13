<?php

use Illuminate\Support\Facades\Route;

// Catch-all route for Vue.js SPA, excluding API routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '(?!api|sanctum).*'); // Exclude /api/* and /sanctum/*
