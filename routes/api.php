<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\Api\AuthController;






Route::prefix('v1')
    ->middleware('api')
    ->group(function () {
        require __DIR__.'/api/v1/auth.php';
        require __DIR__.'/api/v1/customer.php';
        require __DIR__.'/api/v1/admin.php';
    });


    Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->group(function () {
        Route::get('/user', fn (Request $r) => $r->user());
    });

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




