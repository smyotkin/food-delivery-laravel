<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UsersController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/users', [UsersController::class, 'showUsers'])
    ->middleware(['auth'])
    ->name('users');

Route::get('/users/get', [UsersController::class, 'getUsersJSON'])
    ->middleware(['auth'])
    ->name('users/get');

Route::get('/users/add', [UsersController::class, 'addUser'])
    ->middleware(['auth'])
    ->name('users/add');

Route::post('/users/add', [UsersController::class, 'storeUser'])
    ->middleware(['auth']);

Route::get('/users/{id}', [UsersController::class, 'showUser'])
    ->middleware(['auth'])
    ->name('user');

Route::post('/users/update', [UsersController::class, 'updateUser'])
    ->middleware(['auth'])
    ->name('users/update');

require __DIR__.'/auth.php';
