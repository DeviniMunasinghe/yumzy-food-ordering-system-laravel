<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

Route::middleware(['auth:sanctum'])->group(function () {
    //add to cart
    Route::post('/add', [CartController::class, 'addToCart']);

    //view the cart
    Route::get('/view', [CartController::class, 'viewCart']);

    //remove cart items
    Route::delete('/remove-item', [CartController::class, 'removeFromCart']);

    //update cart item quantity
    Route::put('/update-quantity', [CartController::class, 'updateCartItemQuantity']);

    //send selected cart items 
    Route::post('/select-items', [CartController::class, 'selectItems']);

    //fetch selected cart items for checkout with order summary 
    Route::get('/checkout-items', [CartController::class, 'fetchCheckoutItems']);
});