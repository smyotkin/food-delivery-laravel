<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Jenssegers\Date\Date;

Date::setLocale('ru');

class UsersController extends Controller
{
    public function showUsers()
    {
        return view('users/users', [
            'users' => User::all()->sortByDesc('last_seen')
        ]);
    }

    public function showUser(Request $request)
    {
        return view('users/user', [
            'user' => User::where('id', $request->route('id'))->first()
        ]);
    }
}
