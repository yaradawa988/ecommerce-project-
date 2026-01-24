<?php

//use App\Http\Controllers\Api\Customer\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

 Route::prefix('customer')->middleware('check.ability:customer.access')->group(function () {
           // Route::get('profile', [ProfileController::class, 'show']);
            //Route::put('profile', [ProfileController::class, 'update']);
        });
