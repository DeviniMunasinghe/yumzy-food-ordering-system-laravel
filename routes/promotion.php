<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PromotionController;

//add a new promotion
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->group(function () {
    Route::post('/add-new', [PromotionController::class, 'addPromotion']);
});

//get all promotions
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/get-all', [PromotionController::class, 'getAllPromotions']);
});

//get promotion by id
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->group(function () {
    Route::get('/get/{id}', [PromotionController::class, 'getPromotionById']);
});

//apply promotion
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->group(function () {
    Route::post('/apply', [PromotionController::class, 'applyPromotion']);
});

//delete promotion by id
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->group(function () {
    Route::delete('/delete/{id}', [PromotionController::class, 'deletePromotionById']);
});
