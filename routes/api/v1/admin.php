<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CategoryController;


  // ================= Admin =================
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::prefix('categories')->group(function () {
        // List all categories
        Route::get('/', [CategoryController::class, 'index'])->middleware('check.ability:category.view');
        // Create a new category
        Route::post('/', [CategoryController::class, 'store'])->middleware('check.ability:category.create');
        // Show a single category
        Route::get('/{category}', [CategoryController::class, 'show']);
        // Update a category
        Route::put('/{category}', [CategoryController::class, 'update'])->middleware('check.ability:category.update');
        // Delete a category
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('check.ability:category.delete');
    });
});
