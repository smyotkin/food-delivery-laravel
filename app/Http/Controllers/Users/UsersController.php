<?php

namespace App\Http\Controllers\Users;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use Jenssegers\Date\Date;
use Cache;

use App\Services\UsersService;

class UsersController extends Controller
{
    /**
     * Шаблон отображения всех пользователей
     *
     * @return object
     */
    public function showUsers(): object
    {
        return view('users/users');
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

        if ($request->input('query')) {
            $data = User::where('phone', 'like', '%' . $request->input('query') . '%')
                ->orWhere('last_name', 'like', '%' . $request->input('query') . '%')
                ->orderBy('last_seen', 'desc')
                ->orderBy('updated_at', 'desc')
                ->simplePaginate(100);
        } else {
            $data = User::orderBy('last_seen', 'desc')
                ->orderBy('updated_at', 'desc')
                ->simplePaginate(100);
        }

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

        Log::info("Update user: id({$user->id}), first_name({$user->first_name}), last_name({$user->last_name}), city_id({$user->city_id}), position_id({$user->position_id}), phone({$user->phone}), is_active({$user->is_active})");

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
     * Метод добавления нового пользователя
     * @param Request $request
     * @return object
     * @throws \Exception
     */
    public function storeUser(Request $request): object
    {
        $user = UsersService::createUser($request->toArray());

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
