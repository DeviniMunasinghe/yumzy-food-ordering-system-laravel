<?php

use Illuminate\Support\Facades\Route;
Use App\Http\Controllers\ItemController;

// Include authentication routes
Route::prefix('auth')->group(function () {
    require base_path('routes/auth.php');
});

Route::get('/status', function () {
    return response()->json(['status' => 'API is running'], 200);
});

Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->group(function () {
    Route::post('/add-item', [ItemController::class, 'store']);
});