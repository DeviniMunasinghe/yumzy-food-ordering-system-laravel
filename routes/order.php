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

    //delete an order by id
    Route::delete('/delete-order/{id}', [OrderController::class, 'deleteOrder']);

    //update order status by id
    Route::put('/update-status/{id}', [OrderController::class, 'updateOrderStatus']);

    //get an order status count
    Route::get('/status-count', [OrderController::class, 'getOrderStatusCount']);

    //get an order status count percentage
    Route::get('/status-percentage', [OrderController::class, 'getOrderStatusPercentage']);

     //get weekly order summary
     Route::get('/weekly-summary', [OrderController::class, 'getWeeklyOrderSummary']);
});