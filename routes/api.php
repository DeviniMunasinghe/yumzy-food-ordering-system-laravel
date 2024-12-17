<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

//routes for registration
Route::middleware('api')->post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');

//routes for login
Route::middleware('api')->post('/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login');

//routes for forgot password
Route::middleware('api')->post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->name('password.email');

//routes for reset password
Route::middleware('api')->post('/reset-password', [NewPasswordController::class, 'store'])
    ->name('password.update');

//routes for email verification(using 'auth:sanctum' middleware)
Route::middleware(['auth:sanctum', 'signed'])->get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => 'Email verified successfully.']);
})->name('verification.verify');

//route to resend email verification notification(using 'auth:sanctum' middleware)
Route::middleware(['auth:sanctum'])->post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->name('verification.send');

// route to handle authenticated user logout(using 'auth:sanctum' middleware)
Route::middleware(['auth:sanctum'])->post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
