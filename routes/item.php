<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/add-item', [ItemController::class, 'store']);
});
