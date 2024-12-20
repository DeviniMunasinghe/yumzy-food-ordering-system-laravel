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