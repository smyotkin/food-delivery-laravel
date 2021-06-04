<?php

use App\Http\Controllers\Users\UsersController;

Route::middleware(['auth'])->group(function () {
    Route::get('/users/get', [UsersController::class, 'getUsersJSON'])
        ->name('users/get');
});

Route::middleware(['auth', 'last.page'])->group(function () {
    Route::get('/profile', [UsersController::class, 'showProfile'])
        ->name('profile');
    
    Route::get('/users', [UsersController::class, 'showUsers'])
        ->name('users');

    Route::get('/users/add', [UsersController::class, 'addUser'])
        ->name('users/add');

    Route::post('/users/add', [UsersController::class, 'storeUser']);

    Route::get('/users/{id}', [UsersController::class, 'showUser'])
        ->whereNumber('id')
        ->name('user');

    Route::post('/users/update', [UsersController::class, 'updateUser'])
        ->name('users/update');
});

