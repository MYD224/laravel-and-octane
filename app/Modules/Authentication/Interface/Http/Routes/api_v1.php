<?php

use App\Modules\Authentication\Interface\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;




Route::prefix('v1')->group(function () {

    Route::post("register", [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-phone', [AuthController::class, 'verifyPhone']);
    Route::post('verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('forgot-password', [AuthController::class, 'resetPassword']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::patch('edit-password', [AuthController::class, 'editPassword']);

    Route::middleware(['auth:api'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
