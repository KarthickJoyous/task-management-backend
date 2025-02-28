<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Profile\LogoutController;
use App\Http\Controllers\Api\Profile\ProfileController;


Route::group(['middleware' => ['throttle:10,1']], function () { 
    Route::post('register', RegisterController::class);
    Route::post('login', LoginController::class);
});

Route::group(['middleware' => ['auth:sanctum']], function () { 
    Route::get('me', ProfileController::class);
    Route::post('logout', LogoutController::class);
});
