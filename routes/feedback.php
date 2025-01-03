<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;

//add an new item
Route::post('add-feedback', [FeedbackController::class, 'submitFeedback']);