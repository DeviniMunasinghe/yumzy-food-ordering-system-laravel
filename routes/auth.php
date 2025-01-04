<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AdminController;
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

// Route to add a new admin (only accessible to super admin, using 'auth:sanctum' middleware)
Route::middleware(['auth:sanctum', 'role:super_admin,admin'])->post('/add-admin', [AdminController::class, 'addAdmin'])->name('admin.add');

//Route to delete an admin(only super admin can)
Route::middleware(['auth:sanctum', 'role:super_admin'])->delete('/delete-admin/{id}', [AdminController::class, 'deleteAdmin'])->name('admin.delete');

//Route to get all admins(only super admin and admin can)
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->get('/get-all-admins', [AdminController::class, 'getAllAdmins'])->name('admin.get');

//Route to get an admin(only super admin can)
Route::middleware(['auth:sanctum', 'role:super_admin'])->get('/get-admin/{id}', [AdminController::class, 'getAdminById'])->name('admin.get');