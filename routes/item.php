<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

//add an new item
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/add-item', [ItemController::class, 'store']);
});

//get all items
Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/get-all',[ItemController::class,'index']);
});

//get a specific item
Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/get-item/{id}',[ItemController::class,'show']);
});

//delete a specific item by id
Route::middleware(['auth:sanctum'])->group(function(){
    Route::delete('/delete-item/{id}',[ItemController::class,'delete']);
});

//update a specific item by id
Route::middleware(['auth:sanctum'])->group(function(){
    Route::put('/update-item/{id}',[ItemController::class,'update']);
});

//get items by category
Route::get('/category/{category_name}', [ItemController::class, 'getItemsByCategory']);
