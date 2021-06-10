<?php

use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Users\PositionsController;
use App\Http\Controllers\Profile\ProfileController;

Route::middleware(['auth'])->group(function () {
    Route::get('/users/getAjax', [UsersController::class, 'getAjax'])
        ->name('users/getAjax');

    Route::get('/users/getPositionsAjax', [PositionsController::class, 'getAjax'])
        ->name('users/getPositionsAjax');

    Route::middleware(['last.page'])->group(function () {
        Route::resource('users/positions', PositionsController::class)->except([
            'edit'
        ]);

        Route::resource('users', UsersController::class)->except([
            'edit', 'destroy'
        ]);
    });

    //role:position,permission
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])
            ->name('profile');
    });
});


