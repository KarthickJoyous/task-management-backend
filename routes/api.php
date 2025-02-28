<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Profile\LogoutController;
use App\Http\Controllers\Api\Profile\ProfileController;

function resourceNotFound($message) {

    return response()->json([
        'success' => false,
        'message' => $message,
        'code' => 404
    ], 404);
}

Route::group(['middleware' => ['apiLogger']], function () {
    
    Route::group(['middleware' => ['throttle:10,1']], function () { 
        Route::post('register', RegisterController::class);
        Route::post('login', LoginController::class);
    });
    
    Route::group(['middleware' => ['auth:sanctum', 'apiAuthBase']], function () { 
        Route::get('me', ProfileController::class);
        Route::post('logout', LogoutController::class);
    
        Route::apiResource('tasks', TaskController::class)->missing(function () {
            return resourceNotFound(__('messages.task_not_found'));
        });;
    });
});
