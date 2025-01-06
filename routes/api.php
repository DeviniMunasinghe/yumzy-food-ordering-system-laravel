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

// Include feedback routes
Route::prefix('feedback')->group(function () {
    require base_path('routes/feedback.php');
});

// Include feedback routes
Route::prefix('promotion')->group(function () {
    require base_path('routes/promotion.php');
});


// Include search routes
Route::prefix('search')->group(function () {
    require base_path('routes/search.php');
});