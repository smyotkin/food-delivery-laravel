<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\System\NotificationsController;
use App\Http\Controllers\System\EventsController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['user.is_active'])->group(function () {
    require __DIR__.'/users.php';

    Route::middleware(['auth'])->group(function () {

        /**
         * AJAX - Settings
         */
        Route::get('settings/get.ajax', [SettingsController::class, 'getAjax'])
            ->middleware(['permissions:settings_view'])
            ->name('settings/get.ajax');

        Route::post('settings/clear.cache', [SettingsController::class, 'clearCache'])
            ->middleware(['permissions:settings_modify'])
            ->name('settings/clear.cache');

        /**
         * AJAX - Settings/Events
         */
        Route::middleware(['permissions:events_modify_and_view'])->group(function () {
            Route::get('system/events/export.csv', [EventsController::class, 'exportEventsCsv']);

            Route::post('system/events/clear', [EventsController::class, 'clearEvents'])
                ->name('system/events/clear');

            Route::get('system/events/get.ajax', [EventsController::class, 'getEventsAjax'])
                ->name('system/events/get.ajax');
        });

        /**
         * AJAX - Settings/Notifications
         */
        Route::get('system/notifications/get.ajax', [NotificationsController::class, 'getAjax'])
            ->middleware(['permissions:notifications_modify_and_view'])
            ->name('system/notifications/get.ajax');


        /**
         * Middleware - Last Page
         */
        Route::middleware(['last.page'])->group(function () {

            /**
             * Http - Dashboard
             */
            Route::get('dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');

            /**
             * Http - Settings
             */
            Route::get('settings', [SettingsController::class, 'index'])
                ->middleware(['permissions:settings_view'])
                ->name('settings.index');

            Route::match(['put', 'patch'], 'settings/{setting}', [SettingsController::class, 'update'])
                ->middleware(['permissions:settings_modify'])
                ->name('settings.update');

            /**
             * Http - Settings/Events
             */
            Route::get('system/events', [EventsController::class, 'index'])
                ->middleware(['permissions:events_modify_and_view'])
                ->name('events.index');

            /**
             * Http - Settings/Logs
             */
            Route::get('system/logs', [LogViewerController::class, 'index'])
                ->middleware(['permissions:log_modify_and_view'])
                ->name('logs.index');

            /**
             * Http - Settings/Notifications
             */
            Route::resource('system/notifications', NotificationsController::class)
                ->middleware(['permissions:notifications_modify_and_view'])
                ->only([
                    'index',
                    'update',
                ]);
        });
    });
});

