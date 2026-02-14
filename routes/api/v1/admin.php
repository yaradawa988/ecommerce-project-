<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\UserController;

  // ================= Admin =================
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {


             //categories
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


             //products
    Route::prefix('products')->group(function () {

            Route::get('/', [ProductController::class, 'index'])
                ->middleware('check.ability:product.view');

            Route::post('/', [ProductController::class, 'store'])
                ->middleware('check.ability:product.create');

            Route::get('/{product}', [ProductController::class, 'show'])
                ->middleware('check.ability:product.view');

            Route::put('/update/{product}', [ProductController::class, 'update'])
                ->middleware('check.ability:product.update');

            Route::delete('/{product}', [ProductController::class, 'destroy'])
                ->middleware('check.ability:product.delete');
        });
  
          //users

         Route::prefix('users')->group(function () {

        Route::get('/',  [UserController::class, 'index'])->middleware('check.ability:user.view');
        Route::post('/', [UserController::class, 'store'])->middleware('check.ability:user.create');
        Route::get('/{id}', [UserController::class, 'show'])->middleware('check.ability:user.show');
        Route::post('/{id}', [UserController::class, 'update'])->middleware('check.ability:user.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('check.ability:user.delete');
        Route::put('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->middleware('check.ability:user.update');
        Route::get('/export', [UserController::class, 'export'])->middleware('check.ability:user.view');
        Route::get("/export/csv", [UserController::class, "exportCsv"])->middleware('check.ability:user.view');

    });



});
