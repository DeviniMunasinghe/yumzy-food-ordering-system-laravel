<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::middleware(['auth:sanctum'])->group(function () {
    
    //remove selected items from checkout
    Route::post('/remove-selected-items', [OrderController::class, 'removeSelectedItems']);

});