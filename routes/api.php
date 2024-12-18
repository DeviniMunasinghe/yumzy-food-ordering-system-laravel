<?php

use Illuminate\Support\Facades\Route;

// Include authentication routes
Route::prefix('auth')->group(function () {
    require base_path('routes/auth.php');
});

Route::get('/status', function () {
    return response()->json(['status' => 'API is running'], 200);
});
