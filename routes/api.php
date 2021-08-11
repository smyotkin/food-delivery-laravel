<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\UsersController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('users', [UsersController::class, 'index'])
        ->middleware(['permissions:users_view']);

    Route::get('users/{user}', [UsersController::class, 'show'])
        ->middleware(['any-permissions:users_employee_view||users_specialist_view||users_head_view||users_owner_view']);
});
