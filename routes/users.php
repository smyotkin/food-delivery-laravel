<?php

use App\Http\Controllers\Users\UsersController;

Route::middleware(['auth'])->group(function () {
    Route::get('/users/getAjax', [UsersController::class, 'getAjax'])
        ->name('users/getAjax');
});

Route::middleware(['auth', 'last.page'])->group(function () {
    Route::get('/profile', [UsersController::class, 'profile'])
        ->name('profile');

    Route::get('/users', [UsersController::class, 'index'])
        ->name('users');

    Route::get('/users/add', [UsersController::class, 'add'])
        ->name('users/add');

    Route::get('/users/{id}', [UsersController::class, 'detail'])
        ->whereNumber('id')
        ->name('user');

    Route::post('/users/save', [UsersController::class, 'save'])
        ->name('users/save');
});

