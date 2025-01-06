<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

//search items 
Route::get('search-new', [SearchController::class, 'search']);