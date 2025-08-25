<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{Auth\AuthController, Admin\AdminUserController};
use Spatie\Permission\Middleware\RoleMiddleware;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::prefix('admin')->middleware(RoleMiddleware::class . ':admin')->group(function () {
        Route::resource('users', AdminUserController::class)->only([
            'index', 'show', 'store', 'update', 'destroy'
        ]);
    });
});
