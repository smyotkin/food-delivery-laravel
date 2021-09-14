<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Services\PasswordResetsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;
use Throwable;

class PasswordResetLinkController extends Controller
{
    /**
     * Создаем представление для восстановления пароля (валидации номера телефона), в случае успеха - делаем
     * переадресацию
     *
     * @param PasswordResetRequest $request
     * @return RedirectResponse|string
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
     * @throws Exception|Throwable
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
     * @throws Exception|Throwable
     */
    public function sendSmsAjax(PasswordResetRequest $request): void
    {
        $pin = PasswordResetsService::insertPinOrFail($request->phone);

        PasswordResetsService::sendPinViaSms([
            'phone' => $request->phone,
            'pin' => $pin,
        ]);
    }

    /**
     * Проверяем валидность данных (телефон, пин-код и новый пароль) и меняем пароль у пользователя по номеру телефона
     *
     * @param PasswordResetRequest $request
     * @return RedirectResponse
     * @throws Exception|Throwable
     */
    public function store(PasswordResetRequest $request): RedirectResponse
    {
        $lastActiveEntry = PasswordResetsService::getLastActiveEntry($request->phone);

        if (!empty($lastActiveEntry)) {
            $pinValidate = PasswordResetsService::checkPinAttempt($lastActiveEntry, $request);

            if ($pinValidate === true) {
                PasswordResetsService::updateUserPassword([
                    'phone' => $request->phone,
                    'new_password' => $request->new_password,
                    'pin' => $lastActiveEntry->pin_code,
                ]);

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
