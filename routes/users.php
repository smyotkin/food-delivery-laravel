<?php

use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Users\PositionsController;
use App\Http\Controllers\Profile\ProfileController;

Route::middleware(['auth'])->group(function () {
    Route::get('/users/getAjax', [UsersController::class, 'getAjax'])
        ->name('users/getAjax');

    Route::get('/users/positions/getAjax', [PositionsController::class, 'getAjax'])
        ->name('positions.getAjax');

    Route::get('/users/positions/getAjaxByStatus', [PositionsController::class, 'getAjaxByStatus'])
        ->name('positions.getAjaxByStatus');

    Route::get('/users/positions/getWithPermissions', [PositionsController::class, 'getWithPermissions'])
        ->name('positions.getWithPermissions');

    Route::get('/users/getPermissionsCheckedAjax', [UsersController::class, 'getPermissionsCheckedAjax'])
        ->name('users.getPermissionsCheckedAjax');

    Route::middleware(['last.page'])->group(function () {
        Route::resource('users/positions', PositionsController::class)->except([
            'edit'
        ]);

        Route::resource('users', UsersController::class)->except([
            'edit', 'destroy'
        ]);

        /**
         * Формат проверки по должности: role:position,permission
         * Формат проверки по правам: permissions:permission1|permission2|...
         */
        Route::middleware(['permissions:users_specialist_view'])->group(function () {
            Route::get('/profile', [ProfileController::class, 'index'])
                ->name('profile');
        });
    });
});


