<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('simple-register', [AuthController::class, 'simpleRegister']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('forget_password', [AuthController::class, 'forgetPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('set-password', [AuthController::class, 'setPassword']);

    // Route::post('me', [AuthController::class, 'me']);
});

Route::middleware(['auth:api'])->group(function () {
    //Test API
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');

    //Get Profile
    Route::get('get-profile', [AuthController::class, 'getProfile']);
});

