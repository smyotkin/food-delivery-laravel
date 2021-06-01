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
Route::get('/users/{id}', [UsersController::class, 'showUser'])->middleware(['auth'])->name('user');

require __DIR__.'/auth.php';
