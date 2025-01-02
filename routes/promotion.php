<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PromotionController;

//add a new promotion
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/add-new', [PromotionController::class, 'addPromotion']);
});
