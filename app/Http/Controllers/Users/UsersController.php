<?php

namespace App\Http\Controllers\Users;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

//use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use Jenssegers\Date\Date;
use Cache;

class UsersController extends Controller
{
    /**
     * Шаблон отображения всех пользователей
     *
     * @return object
     */
    public function showUsers(): object
    {
        return view('users/users', [
            'users' => User::all()->sortByDesc('last_seen'),
        ]);
    }

    /**
     * Возвращает список пользователей в JSON
     *
     * @param Request $request
     * @return string
     */
    public function getUsersJSON(Request $request): string
    {
        $users = User::all()->sortByDesc('last_seen')->skip(0)->take(100);

        return $users->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Возвращает список пользователей в таблице, для AJAX
     *
     * @param Request $request
     * @return string
     */
    public function getUsersAJAX(Request $request): string
    {
        $data = User::orderBy('last_seen', 'DESC')->paginate(100);

        return view('users/users-table', compact('data'))->render();
    }

    /**
     * Шаблон отображения пользователя по id
     *
     * @param Request $request
     * @return object
     */
    public function showUser(Request $request): object
    {
        return view('users/user', [
            'user' => User::find($request->route('id')),
        ]);
    }

    /**
     * Редактирование пользователя по id
     *
     * @param Request $request
     * @return object
     */
    public function updateUser(Request $request): object
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

    /**
     * Шаблон добавления пользователя
     *
     * @param Request $request
     * @return object
     */
    public function addUser(Request $request): object
    {
        return view('users/user-add');
    }

    /**
     * Шаблон добавления пользователя
     * @param Request $request
     * @return object
     * @throws \Exception
     */
    public function storeUser(Request $request): object
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

    /**
     * Шаблон профиля пользователя
     *
     * @param Request $request
     * @return object
     */
    public function showProfile(Request $request): object
    {
        return view('users/profile', [
            'user' => User::find(Auth::user()->id),
        ]);
    }
}
