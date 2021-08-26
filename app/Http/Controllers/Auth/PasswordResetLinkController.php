<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Services\PasswordResetsService;
use Exception;

class PasswordResetLinkController extends Controller
{
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
        $this->sendSmsAjax($request);

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
            PasswordResetsService::sendPinViaSms([
                'phone' => $request->phone,
                'pin' => $pin,
            ]);

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
                $updateUserPassword = PasswordResetsService::updateUserPassword([
                    'phone' => $request->phone,
                    'new_password' => $request->new_password,
                    'pin' => $lastActiveEntry->pin_code,
                ]);

                return $updateUserPassword ? redirect()->route('login', ['password_reset_success' => true]) : false;
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
