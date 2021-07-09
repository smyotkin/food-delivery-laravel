<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
                ->middleware('guest')
                ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
                ->middleware('guest');

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
                ->middleware('guest')
                ->name('password.request');

Route::get('/forgot-password/{phone}', [PasswordResetLinkController::class, 'createForm'])
                ->middleware('guest')
                ->name('password.phone');

Route::post('/forgot-password/{phone}', [PasswordResetLinkController::class, 'sendSmsAjax'])
                ->middleware('guest')
                ->name('password.pin');

Route::put('/forgot-password/{phone}', [PasswordResetLinkController::class, 'store'])
                ->middleware('guest')
                ->name('password.store');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
                ->middleware('guest')
                ->name('password.update');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');
