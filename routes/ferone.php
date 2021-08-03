<?php

use App\Http\Controllers\DashboardController;
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
    require __DIR__.'/users.php';

    Route::middleware(['auth', 'last.page'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/settings/get.ajax', [SettingsController::class, 'getAjax'])
            ->name('settings/get.ajax');
        Route::post('settings/clear.cache', [SettingsController::class, 'clearCache'])
            ->name('settings/clear.cache');
        Route::resource('settings', SettingsController::class)->only([
            'index', 'update'
        ]);

        Route::middleware(['permissions:events_modify_and_view'])->group(function () {
            Route::get('system/events', [EventsController::class, 'index'])
                ->name('events.index');
            Route::get('system/events/export.csv', [EventsController::class, 'exportEventsCsv']);
            Route::post('system/events/clear', [EventsController::class, 'clearEvents'])
                ->name('system/events/clear');
            Route::get('system/events/get.ajax', [EventsController::class, 'getEventsAjax'])
                ->name('system/events/get.ajax');
        });

        Route::middleware(['permissions:log_modify_and_view'])->group(function () {
            Route::get('system/logs', [LogViewerController::class, 'index'])
                ->name('logs.index');
        });

        Route::middleware(['permissions:notifications_modify_and_view'])->group(function () {
            Route::get('system/notifications/get.ajax', [NotificationsController::class, 'getAjax'])
                ->name('system/notifications/get.ajax');
            Route::resource('system/notifications', NotificationsController::class)->only([
                'index', 'update'
            ]);
        });
    });
});

