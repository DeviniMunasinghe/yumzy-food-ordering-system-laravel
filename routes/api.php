<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

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

Route::get('/status', function () {
    return response()->json(['status' => 'API is running'], 200);
});
