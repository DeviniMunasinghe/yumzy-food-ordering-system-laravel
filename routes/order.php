<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::middleware(['auth:sanctum'])->group(function () {
    
    //remove selected items from checkout
    Route::post('/remove-selected-items', [OrderController::class, 'removeSelectedItems']);

    //place an order
    Route::post('/place', [OrderController::class, 'placeOrder']);

    //get all orders
    Route::get('/get-all', [OrderController::class, 'getAllOrders']);

     //get an order by id
     Route::get('/get-order/{id}', [OrderController::class, 'getOrderById']);

});