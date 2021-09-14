<?php

use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Users\PositionsController;
use App\Http\Controllers\Profile\ProfileController;

/**
 * Формат проверки по должности: role:position,permission
 * Формат проверки по правам: permissions:permission1|permission2|...
 */

Route::middleware(['auth'])->group(function () {
    /**
     * AJAX - Users
     */
    Route::get('/users/get.ajax', [UsersController::class, 'getAjax'])
        ->name('users/get.ajax');

    Route::get('/users/form/get.ajax', [UsersController::class, 'getUserFormAjax'])
        ->name('users/form/get.ajax');

    Route::get('/users/permissions/get.ajax', [UsersController::class, 'getPermissionsCheckedAjax'])
        ->name('users/permissions/get.ajax');

    Route::get('/users/export.csv', [UsersController::class, 'exportCsv']);

    /**
     * AJAX - User Profile
     */
    Route::get('/profile/get.ajax', [ProfileController::class, 'getAjax'])
        ->name('profile/get.ajax');

    /**
     * AJAX - Positions
     */
    Route::get('/users/positions/get.ajax', [PositionsController::class, 'getAjax'])
        ->middleware(['permissions:users_positions_view'])
        ->name('positions/get.ajax');

    Route::get('/users/positions/form/get.ajax', [PositionsController::class, 'getFormAjax'])
        ->name('positions/form/get.ajax');

    Route::get('/users/positions/select/get.ajax', [PositionsController::class, 'getSelectAjax'])
        ->name('positions/select/get.ajax');

    /**
     * Http - Positions
     */
    Route::middleware(['last.page'])->group(function () {
        Route::middleware(['permissions:users_position_create'])->group(function () {
            Route::get('users/positions/create', [PositionsController::class, 'create'])
                ->name('positions.create');

            Route::post('users/positions', [PositionsController::class, 'store'])
                ->name('positions.store');
        });

        Route::get('users/positions', [PositionsController::class, 'index'])
            ->middleware(['permissions:users_positions_view'])
            ->name('positions.index');

        Route::get('users/positions/{role}', [PositionsController::class, 'show'])
            ->middleware(['any-permissions:users_position_view||users_position_modify'])
            ->name('positions.show');

        Route::match(['put', 'patch'], 'users/positions/{position}', [PositionsController::class, 'update'])
            ->middleware(['permissions:users_position_modify'])
            ->name('positions.update');

        Route::delete('users/positions/{position}', [PositionsController::class, 'destroy'])
            ->middleware(['permissions:users_position_delete'])
            ->name('positions.destroy');
    });

    /**
     * Http - Users
     */
    Route::middleware(['last.page'])->group(function () {
        Route::middleware(['any-permissions:users_employee_add||users_specialist_add||users_head_add||users_owner_add'])->group(function () {
            Route::get('users/create', [UsersController::class, 'create'])
                ->name('users.create');

            Route::post('users', [UsersController::class, 'store'])
                ->name('users.store');
        });

        Route::get('users', [UsersController::class, 'index'])
            ->middleware(['permissions:users_view'])
            ->name('users.index');

        Route::get('users/{user}', [UsersController::class, 'show'])
            ->middleware(['any-permissions:users_employee_view||users_specialist_view||users_head_view||users_owner_view||users_employee_modify||users_specialist_modify||users_head_modify||users_owner_modify'])
            ->name('users.show');

        Route::match(['put', 'patch'], 'users/{user}', [UsersController::class, 'update'])
            ->middleware(['any-permissions:users_employee_modify||users_specialist_modify||users_head_modify||users_owner_modify'])
            ->name('users.update');

        Route::delete('users/{user}', [UsersController::class, 'destroy'])
            ->middleware(['any-permissions:users_employee_delete||users_specialist_delete||users_head_delete||users_owner_delete'])
            ->name('users.destroy');
    });

    /**
     * Http - User Profile
     */
    Route::middleware(['last.page'])->group(function () {
        Route::resource('profile', ProfileController::class)->only([
            'index', 'update'
        ]);
    });

});


