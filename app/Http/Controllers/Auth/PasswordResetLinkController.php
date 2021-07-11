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
use Illuminate\Support\Facades\Validator;

class PasswordResetLinkController extends Controller
{
    public static $attemptsPerDay = 10;
    public static $pinLifetimeMinutes = 5;
    public static $sendSms = 0;

    /**
     * Проверяем срок действия активных записей при каждом использовании класса
     *
     * @throws Exception
     */
    public function __construct()
    {
        self::$attemptsPerDay = config('custom.password_resets.attempts_per_day') ?? self::$attemptsPerDay;
        self::$pinLifetimeMinutes = config('custom.password_resets.pin_lifetime_minutes') ?? self::$pinLifetimeMinutes;
        self::$sendSms = config('custom.password_resets.send_sms') ?? self::$sendSms;

        $this->checkActiveEntries();
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
        $todayEntries = $this->getTodayEntries($request->phone);
        $lastActiveEntry = $this->getlastActiveEntry($request->phone);
        $endPinTime = !empty($lastActiveEntry) ? Carbon::createFromTimeString($lastActiveEntry->created_at)->addMinutes
        (self::$pinLifetimeMinutes) : false;

        return view('auth.forgot-password', [
            'phone' => $request->phone,
            'pin_created' => $lastActiveEntry->created_at ?? false,
            'pin_ended' => $endPinTime,
            'last_active_entry' => $lastActiveEntry,
            'attempts' => self::$attemptsPerDay - $todayEntries->count(),
            'pin_activity_time' => Carbon::now()->diff($endPinTime)->format('%I:%S'),
            'pin_attempts' => !empty($lastActiveEntry) ? 3 - Cache::get("pin_attempts-{$lastActiveEntry->id}") : false,
        ]);
    }

    /**
     * Проверяет срок действия активных записей и при истечении срока меняет статус is_active на 0
     *
     * @return bool|object
     * @throws Exception
     */
    public function checkActiveEntries()
    {
        return DB::table('sent_pin')
                ->where('is_active', 1)
                ->whereRaw('DATE_ADD(`created_at`, INTERVAL ' . self::$pinLifetimeMinutes . ' MINUTE) < \'' . Carbon::now() . '\'')
                ->update(['is_active' => 0]);
    }

    /**
     * Добавляет запись в таблицу пинов если не превышен лимит в сутки и отсутствует последнаяя запись или последная
     * запись не активна
     *
     * @param $phone
     * @return bool
     * @throws Exception
     */
    private function insertPinOrFail($phone)
    {
        $todayEntries = $this->getTodayEntries($phone);
        $lastEntry = !empty($todayEntries) ? $todayEntries->first() : null;

        if ($todayEntries->count() < self::$attemptsPerDay && (empty($lastEntry) || $lastEntry->is_active == 0)) {
            return DB::table('sent_pin')->insert([
                'phone' => $phone,
                'pin_code' => $pin = $this->generatePin(),
                'created_at' => Carbon::now(),
                'is_active' => 1,
            ]) ? $pin : false;
        }

        return false;
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
        if ($pin = $this->insertPinOrFail($request->phone)) {
            if (self::$sendSms) {
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
     * Генерация 4-х значного пин-кода
     *
     * @return string
     * @throws Exception
     */
    private function generatePin()
    {
        return str_pad(random_int(100, 9999), 4, 0, STR_PAD_LEFT);
    }

    /**
     * Получить все записи за сутки по номеру телефона
     *
     * @return string
     * @throws Exception
     */
    private function getTodayEntries($phone)
    {
        return DB::table('sent_pin')
                ->where('phone', $phone)
                ->whereDate('created_at', Carbon::today())
                ->orderBy('created_at','desc')
                ->get();
    }

    /**
     * Возвращает последнюю активную запись по номеру телефона
     *
     * @param $phone
     * @return object|bool
     */
    private function getLastActiveEntry($phone)
    {
        return DB::table('sent_pin')
                ->where('phone', $phone)
                ->where('is_active', 1)
                ->orderBy('created_at','desc')
                ->first();
    }

    /**
     * Проверяет количество неправильных попыток ввода для активного пин-кода
     *
     * @param $lastActiveEntry
     * @param $request
     * @return bool|\Illuminate\Validation\Validator
     */
    public function checkPinAttempt($lastActiveEntry, $request)
    {
        $pinValidate = Validator::make($request->all(), [
            'pin' => 'required|digits:4|in:' . $lastActiveEntry->pin_code,
        ]);
        $cacheTitle = "pin_attempts-{$lastActiveEntry->id}";
        $activePinAttempts = Cache::get($cacheTitle) ?? 0;

        if ($pinValidate->fails() && $request != $lastActiveEntry->pin_code) {
            if ($activePinAttempts < 3) {
                Cache::put($cacheTitle, $activePinAttempts + 1, Carbon::now()->addMinutes(self::$pinLifetimeMinutes));
            }

            return $pinValidate;
        }

        if ($activePinAttempts >= 3) {
            return false;
        }

        return true;
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
        $lastActiveEntry = $this->getLastActiveEntry($request->phone);

        if (!empty($lastActiveEntry)) {
            $pinValidate = $this->checkPinAttempt($lastActiveEntry, $request);

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
