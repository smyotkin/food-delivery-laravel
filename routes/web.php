<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UsersController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/users', [UsersController::class, 'showUsers'])->middleware(['auth'])->name('users');
Route::get('/users/add', [UsersController::class, 'addUser'])->middleware(['auth'])->name('users/add');
Route::post('/users/add', [UsersController::class, 'storeUser'])->middleware(['auth'])->name('users/store');
Route::get('/users/{id}', [UsersController::class, 'showUser'])->middleware(['auth'])->name('user');
Route::post('/users/update', [UsersController::class, 'updateUser'])->middleware(['auth'])->name('users/update');

require __DIR__.'/auth.php';
