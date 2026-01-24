<?php

use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\Api\Admin\DashboardController;
//use App\Http\Controllers\Api\Admin\ProductController;

  // ================= Admin =================
        Route::prefix('admin')->middleware('check.ability:admin.access')->group(function () {
            //Route::get('dashboard', [DashboardController::class, 'index']);

            // Example product routes
            //Route::post('products', [\App\Http\Controllers\Api\Admin\ProductController::class, 'store'])
              //  ->middleware('check.ability:product.create');

           // Route::put('products/{product}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'update'])
              //  ->middleware('check.ability:product.update');

            //Route::delete('products/{product}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'destroy'])
               // ->middleware('check.ability:product.delete');
        });
    