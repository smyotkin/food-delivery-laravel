<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\System\NotificationsController;
use App\Http\Controllers\System\EventsController;
use App\Http\Controllers\System\LogController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

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

        Route::get('system/events', [EventsController::class, 'index'])
            ->name('events.index');
        Route::get('system/events/export.csv', [EventsController::class, 'exportEventsCsv']);
        Route::post('system/events/clear', [EventsController::class, 'clearEvents'])
            ->name('system/events/clear');
        Route::get('system/events/get.ajax', [EventsController::class, 'getEventsAjax'])
            ->name('system/events/get.ajax');

        Route::get('system/log', [LogController::class, 'index'])
            ->name('log.index');
        Route::post('system/log/clear', [LogController::class, 'clear'])
            ->name('system/log/clear');
        Route::get('system/log/get.ajax', [LogController::class, 'getAjax'])
            ->name('system/log/get.ajax');

        Route::get('system/logs', [LogViewerController::class, 'index'])
            ->name('logs.index');

        Route::get('system/notifications', [NotificationsController::class, 'index'])
            ->name('notifications.index');
    });
});

