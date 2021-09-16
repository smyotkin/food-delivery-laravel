<?php

namespace App\Services;

use App\Http\Requests\Auth\PasswordResetRequest;
use App\Models\SentPin;
use App\Models\User;
use App\Notifications\SmsCenter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use Throwable;

class PasswordResetsService
{
    public static $attemptsPerDay = 10;
    public static $pinLifetimeMinutes = 5;
    public static $sendSms = 1;

    /**
     * Проверяет срок действия активных записей и при истечении срока меняет статус is_active на 0
     *
     * @throws Exception|Throwable
     */
    public static function checkActiveEntries(): void
    {
        SentPin::where('is_active', 1)
            ->whereRaw('DATE_ADD(`created_at`, INTERVAL ' . config('custom.password_resets.pin_lifetime_minutes', static::$pinLifetimeMinutes) . ' MINUTE) < \'' . Carbon::now() . '\'')
            ->update(['is_active' => 0]);
    }

    /**
     * Добавляет запись в таблицу пинов если не превышен лимит в сутки и отсутствует последнаяя запись или последная
     * запись не активна
     *
     * @param string $phone
     * @return string
     * @throws Exception|Throwable
     */
    public static function insertPinOrFail(string $phone): string
    {
        self::checkActiveEntries();

        $todayEntries = self::getTodayEntries($phone);
        $lastEntry = !empty($todayEntries) ? $todayEntries->first() : null;
        $pin = self::generatePin();

        if ($todayEntries->count() >= config('custom.password_resets.attempts_per_day', static::$attemptsPerDay)) {
            SystemService::createEvent('password_reset_limit', ['phone' => $phone]);

            abort(403, 'Превышен лимит попыток');
        }

        if ($todayEntries->count() < config('custom.password_resets.attempts_per_day', static::$attemptsPerDay) && (empty($lastEntry) || $lastEntry->is_active == 0)) {
            SentPin::create([
                'phone' => $phone,
                'pin_code' => $pin,
                'created_at' => Carbon::now(),
                'is_active' => 1,
            ]);
        } else {
            abort(403, 'Невозможно создать новый пин-код');
        }

        return $pin;
    }

    /**
     * Генерация 4-х значного пин-кода
     *
     * @return string
     * @throws Exception
     */
    public static function generatePin(): string
    {
        return str_pad(random_int(100, 9999), 4, 0, STR_PAD_LEFT);
    }

    /**
     * Получить все записи за сутки по номеру телефона
     *
     * @param string $phone
     * @return Collection
     */
    public static function getTodayEntries(string $phone): Collection
    {
        return SentPin::where('phone', $phone)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Возвращает последнюю активную запись по номеру телефона
     *
     * @param string $phone
     * @return SentPin|null
     */
    public static function getLastActiveEntry(string $phone): ?SentPin
    {
        return SentPin::where('phone', $phone)
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Проверяет количество неправильных попыток ввода для активного пин-кода
     *
     * @param SentPin|null          $lastActiveEntry
     * @param PasswordResetRequest  $request
     * @return mixed
     */
    public static function checkPinAttempt(?SentPin $lastActiveEntry, PasswordResetRequest $request)
    {
        $pinValidate = Validator::make($request->all(), [
            'pin' => 'required|digits:4|in:' . $lastActiveEntry->pin_code,
        ]);
        $cacheTitle = "pin_attempts-{$lastActiveEntry->id}";
        $activePinAttempts = Cache::get($cacheTitle) ?? 0;

        if ($pinValidate->fails() && $request != $lastActiveEntry->pin_code) {
            if ($activePinAttempts < 3) {
                Cache::put($cacheTitle, $activePinAttempts + 1, Carbon::now()->addMinutes(config('custom.password_resets.pin_lifetime_minutes', static::$pinLifetimeMinutes)));
            }

            return $pinValidate;
        }

        if ($activePinAttempts >= 3) {
            return false;
        }

        return true;
    }

    /**
     * Отправка Pin по смс
     *
     * @param array $array
     */
    public static function sendPinViaSms(array $array): void
    {
        if (config('custom.password_resets.send_sms', static::$sendSms)) {
            $user = User::where('phone', $array['phone'])->firstOrFail();

            $user->notify(new SmsCenter([
                'msg' => "Код для восстановления:\n",
                'password' => $array['pin'],
            ]));
        }
    }

    /**
     * Меняем пароль у пользователя по номеру телефона
     *
     * @param array $array
     * @throws Throwable
     */
    public static function updateUserPassword(array $array): void
    {
        $user = User::where('phone', $array['phone'])->firstOrFail();

        DB::transaction(function() use ($user, $array) {
            $user->update([
                'password' => Hash::make($array['new_password']),
            ]);

            $user->saveOrFail();

            DB::table('password_resets')->insert([
                'phone' => $array['phone'],
                'token' => $array['pin'],
                'created_at' => Carbon::now(),
            ]);

            DB::table('sent_pin')
                ->where('phone', $array['phone'])
                ->where('is_active', 1)
                ->update(['is_active' => 0]);
        });

        SystemService::createEvent('password_reset_success', ['phone' => $array['phone']]);
    }
}
