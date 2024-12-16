<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// All API routes should be within the 'api' middleware group to avoid CSRF issues

Route::middleware('api')->post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');

Route::middleware('api')->post('/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login');

Route::middleware('api')->post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->name('password.email');

Route::middleware('api')->post('/reset-password', [NewPasswordController::class, 'store'])
    ->name('password.update');

// Ensure this route is under 'auth:sanctum' middleware if you need token-based authentication
Route::middleware(['auth:sanctum'])->get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->name('verification.verify');

Route::middleware(['auth:sanctum'])->post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->name('verification.send');

// This route uses 'auth:sanctum' middleware to handle authenticated user logout
Route::middleware(['auth:sanctum'])->post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
