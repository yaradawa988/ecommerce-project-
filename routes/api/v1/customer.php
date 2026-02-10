<?php

//use App\Http\Controllers\Api\Customer\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CategoryController;

 Route::prefix('customer')->middleware('check.ability:customer.access')->group(function () {
           // Route::get('profile', [ProfileController::class, 'show']);
           //Route::put('profile', [ProfileController::class, 'update']);
Route::prefix('categories')->group(function () {
           // List all categories
           Route::get('/', [CategoryController::class, 'index']);
           // Show a single category
           Route::get('/{category}', [CategoryController::class, 'show']);
});
        });


      