<?php

use Illuminate\Support\Facades\Route;

// Include authentication routes
Route::prefix('auth')->group(function () {
    require base_path('routes/auth.php');
});

// Include item routes
Route::prefix('items')->group(function () {
    require base_path('routes/item.php');
});

// Include cart routes
Route::prefix('cart')->group(function () {
    require base_path('routes/cart.php');
});

// Include order routes
Route::prefix('order')->group(function () {
    require base_path('routes/order.php');
});

Route::get('/status', function () {
    return response()->json(['status' => 'API is running'], 200);
});
