<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/add', [CartController::class, 'addToCart']);
    Route::get('/view', [CartController::class, 'viewCart']);
});