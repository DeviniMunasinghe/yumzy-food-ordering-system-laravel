<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::get('/cart/view', [CartController::class, 'viewCart']);
});