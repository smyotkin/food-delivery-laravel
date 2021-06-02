<?php

namespace App\Http\Controllers\Users;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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

    public function updateUser(Request $request)
    {
        $phone = User::toDigit($request->phone);
        $request->merge(array('phone' => $phone));

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|digits:11',
        ]);

        $user = User::find($request->id);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        $user->save();

        return redirect()->route('user', ['id' => $request->id]);
    }

    public function addUser(Request $request)
    {
        return view('users/user-add');
    }

    public function storeUser(Request $request)
    {
        $phone = User::toDigit($request->phone);
        $request->merge(array('phone' => $phone));

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|digits:11',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'city_id' => 0,
            'position_id' => 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'phone' => $request->phone,
            'password' => Hash::make(random_int(100000, 999999)),
        ]);

        event(new Registered($user));

        return redirect()->route('user', ['id' => $user->id]);
    }
}
