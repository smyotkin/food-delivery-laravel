<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Models\User;
use App\Notifications\SmsCenter;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\PasswordResetsService;

class PasswordResetLinkController extends Controller
{

    /**
     * Проверяем срок действия активных записей при каждом использовании класса
     *
     * @throws Exception
     */
    public function __construct()
    {
        PasswordResetsService::checkActiveEntries();
    }

    /**
     * Создаем представление для восстановления пароля (валидации номера телефона), в случае успеха - делаем
     * переадресацию
     *
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\RedirectResponse|string
     * @throws Exception
     */
    public function create(PasswordResetRequest $request)
    {
        return !empty($request->phone) ?
            redirect()->route('password.phone', [
                'phone' => $request->phone,
            ]) : view('auth.forgot-password');
    }

    /**
     * Проверяем валидность телефона и создаем форму ввода пина и нового пароля в случае успеха
     *
     * @param PasswordResetRequest $request
     * @return string
     * @throws Exception
     */
    public function createForm(PasswordResetRequest $request)
    {
        $todayEntries = PasswordResetsService::getTodayEntries($request->phone);
        $lastActiveEntry = PasswordResetsService::getlastActiveEntry($request->phone);
        $endPinTime = !empty($lastActiveEntry) ? Carbon::createFromTimeString($lastActiveEntry->created_at)->addMinutes
        (PasswordResetsService::$pinLifetimeMinutes) : false;

        return view('auth.forgot-password', [
            'phone' => $request->phone,
            'pin_created' => $lastActiveEntry->created_at ?? false,
            'pin_ended' => $endPinTime,
            'last_active_entry' => $lastActiveEntry,
            'attempts' => PasswordResetsService::$attemptsPerDay - $todayEntries->count(),
            'pin_activity_time' => Carbon::now()->diff($endPinTime)->format('%I:%S'),
            'pin_attempts' => !empty($lastActiveEntry) ? 3 - Cache::get("pin_attempts-{$lastActiveEntry->id}") : false,
        ]);
    }

    /**
     * Отправляет СМС если добавилась запись в таблицу пинов
     *
     * @param PasswordResetRequest $request
     * @return bool|string
     * @throws Exception
     */
    public function sendSmsAjax(PasswordResetRequest $request)
    {
        if ($pin = PasswordResetsService::insertPinOrFail($request->phone)) {
            if (PasswordResetsService::$sendSms) {
                $user = User::where('phone', $request->phone)->first();

                $user->notify(new SmsCenter([
                    'msg' => "Код для восстановления:\n",
                    'password' => $pin,
                ]));
            }

            return json_encode([
                'success' => true,
            ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        }

        return false;
    }

    /**
     * Проверяем валидность данных (телефон, пин-код и новый пароль) и меняем пароль у пользователя по номеру телефона
     *
     * @param PasswordResetRequest $request
     * @return bool|\Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function store(PasswordResetRequest $request)
    {
        $lastActiveEntry = PasswordResetsService::getLastActiveEntry($request->phone);

        if (!empty($lastActiveEntry)) {
            $pinValidate = PasswordResetsService::checkPinAttempt($lastActiveEntry, $request);

            if ($pinValidate === true) {
                $user = User::where('phone', $request->phone)->first();

                $user->update([
                    'password' => Hash::make($request->new_password),
                ]);

                if ($user->save()) {
                    DB::table('password_resets')->insert([
                        'phone' => $request->phone,
                        'token' => $lastActiveEntry->pin_code,
                        'created_at' => Carbon::now(),
                    ]);
                }

                DB::table('sent_pin')
                    ->where('phone', $request->phone)
                    ->where('is_active', 1)
                    ->update(['is_active' => 0]);

                return redirect()->route('login', ['password_reset_success' => true]);
            } else {
                return redirect()
                    ->route('password.phone', ['phone' => $request->phone])
                    ->withErrors($pinValidate)
                    ->withInput();
            }
        }

        return redirect()->route('password.request', ['failed' => true]);
    }
}
