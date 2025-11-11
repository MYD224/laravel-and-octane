<?php

use App\Modules\Authentication\Interface\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;
use App\Modules\Post\Interface\Http\Controllers\Api\V1\PostController;




Route::prefix('v1')->group(function () { 

    Route::post("register", [AuthController::class, 'register'] );
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware(['auth:api'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::resource("/posts", PostController::class);
    });
    
    Route::post('/verify-phone', [AuthController::class, 'verifyPhone']);
    
    
});

