<?php

use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Profile\ProfileController;

Route::middleware(['auth'])->group(function () {
    Route::get('/users/getAjax', [UsersController::class, 'getAjax'])
        ->name('users/getAjax');

    Route::middleware(['last.page'])->group(function () {
        Route::get('/users', [UsersController::class, 'index'])
            ->name('users');

        Route::get('/users/create', [UsersController::class, 'create'])
            ->name('users/create');

        Route::post('/users', [UsersController::class, 'store'])
            ->name('users/store');

        Route::get('/users/{id}', [UsersController::class, 'show'])
            ->whereNumber('id')
            ->name('user');

        Route::post('/users/{id}', [UsersController::class, 'update'])
            ->name('users/update');

        Route::get('/profile', [ProfileController::class, 'index'])
            ->name('profile');
    });


});


