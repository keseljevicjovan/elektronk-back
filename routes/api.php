<?php

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;
use App\Http\Controllers\Api\{
    Auth\AuthController, Image\ImageController, Admin\AdminUserController, Admin\AdminImageController
};

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::resource('images', ImageController::class)->only([
    'index', 'show'
]);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::prefix('admin')->middleware(RoleMiddleware::class . ':admin')->group(function () {
        Route::resource('users', AdminUserController::class)->only([
            'index', 'show', 'store', 'update', 'destroy'
        ]);
        Route::resource('images', AdminImageController::class)->only([
            'index', 'show', 'store', 'update', 'destroy'
        ]);
    });
});
