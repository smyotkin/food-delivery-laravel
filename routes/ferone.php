<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SystemEventsController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['user.is_active'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'last.page'])->name('dashboard');

    require __DIR__.'/users.php';

    Route::middleware(['auth', 'last.page'])->group(function () {
        Route::get('/settings/get.ajax', [SettingsController::class, 'getAjax'])
            ->name('settings/get.ajax');

        Route::post('settings/clear.cache', [SettingsController::class, 'clearCache'])
            ->name('settings/clear.cache');

        Route::resource('settings', SettingsController::class)->only([
            'index', 'update'
        ]);

        Route::resource('system/events', SystemEventsController::class)->only([
            'index'
        ]);

        Route::get('system/events/export.csv', [SystemEventsController::class, 'exportEventsCsv']);

        Route::get('system/events/get.ajax', [SystemEventsController::class, 'getEventsAjax'])
            ->name('system/events/get.ajax');
    });
});

