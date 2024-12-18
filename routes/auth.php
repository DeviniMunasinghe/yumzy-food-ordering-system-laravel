<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

//user registration route
Route::post('/register', [RegisteredUserController::class, 'store'])->name('auth.register');

//user login route
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('auth.login');

//forgot-password route
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('auth.password.email');

//reset password route
Route::post('/reset-password', [PasswordResetLinkController::class, 'store'])->name('auth.password.update');

//email verification route(using 'auth:sanctum' middleware)
Route::middleware(['auth:sanctum', 'signed'])->get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => 'Email verified successfully.']);
})->name('verification.verify');

//route to resend email verification notification(using 'auth:sanctum' middleware)
Route::middleware(['auth:sanctum'])->post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->name('verification.send');

// route to handle authenticated user logout(using 'auth:sanctum' middleware)
Route::middleware(['auth:sanctum'])->post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('auth.logout');
