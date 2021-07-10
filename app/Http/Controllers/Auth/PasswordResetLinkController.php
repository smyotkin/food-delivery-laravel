<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\PasswordResetRequest;
use App\Models\User;
use App\Notifications\SmsCenter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordResetLinkController extends Controller
{
    public static $userPhone = null;
    public static $attemptsPerDay = 10;
    public static $pinLifetimeMinutes = 5;

    /**
     * Создаем представление для восстановления пароля (валидации номера телефона), в случае успеха - делаем
     * переадресацию
     *
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\RedirectResponse|string
     * @throws \Exception
     */
    public function create(PasswordResetRequest $request)
    {
        $this->checkActiveEntries();

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
     * @throws \Exception
     */
    public function createForm(PasswordResetRequest $request)//: string
    {
        self::$userPhone = $request->phone;

        $this->checkActiveEntries();

        $todayEntries = $this->getTodayEntries();
        $lastActiveEntry = $this->getlastActiveEntry();
        $endPinTime = !empty($lastActiveEntry) ? Carbon::createFromTimeString($lastActiveEntry->created_at)->addMinutes
        (self::$pinLifetimeMinutes) : false;

        return view('auth.forgot-password', [
            'phone' => $request->phone,
            'pin_created' => $lastActiveEntry->created_at ?? false,
            'pin_ended' => $endPinTime,
            'last_active_entry' => $lastActiveEntry,
            'attempts' => self::$attemptsPerDay - $todayEntries->count(),
            'pin_activity_time' => Carbon::now()->diff($endPinTime)->format('%I:%S'),
        ]);
    }

    /**
     * Проверяет срок действия активных записей и при истечении срока меняет статус is_active на 0
     *
     * @return bool|object
     * @throws \Exception
     */
    public function checkActiveEntries()
    {
        return DB::table('sent_pin')
                ->where('is_active', 1)
                ->whereRaw('DATE_ADD(`created_at`, INTERVAL ' . self::$pinLifetimeMinutes . ' MINUTE) < \'' . Carbon::now() . '\'')
                ->update(['is_active' => 0]);
    }

    /**
     * Добавляет запись в таблицу пинов если не превышен лимит, последнаяя запись отсутствует или последная запись не
     * активна
     *
     * @return bool
     * @throws \Exception
     */
    public function insertPinOrFail()
    {
        $this->checkActiveEntries();

        $todayEntries = $this->getTodayEntries();
        $lastEntry = !empty($todayEntries) ? $todayEntries->first() : null;

        if ($todayEntries->count() < self::$attemptsPerDay && (empty($lastEntry) || $lastEntry->is_active == 0)) {
            return DB::table('sent_pin')->insert([
                'phone' => self::$userPhone,
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
     * @throws \Exception
     */
    public function sendSmsAjax(PasswordResetRequest $request)
    {
        self::$userPhone = $request->phone;

        $this->checkActiveEntries();

        if ($pin = $this->insertPinOrFail()) {
            $user = User::where('phone', $request->phone)->first();

            $user->notify(new SmsCenter([
                'msg' => "Код для восстановления:\n",
                'password' => $pin,
            ]));

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
     * @throws \Exception
     */
    public function generatePin()
    {
        return str_pad(random_int(100, 9999), 4, 0, STR_PAD_LEFT);
    }

    /**
     * Получить все записи за сутки по номеру телефона
     *
     * @return string
     * @throws \Exception
     */
    public function getTodayEntries()
    {
        if (!empty(self::$userPhone)) {
            return DB::table('sent_pin')
                    ->where('phone', self::$userPhone)
                    ->whereDate('created_at', Carbon::today())
                    ->orderBy('created_at','desc')
                    ->get();
        } else {
            return false;
        }
    }

    /**
     * Возвращает последнюю активную запись по номеру телефона
     *
     * @return object|bool
     */
    public function getLastActiveEntry()
    {
        if (!empty(self::$userPhone)) {
            return DB::table('sent_pin')
                    ->where('phone', self::$userPhone)
                    ->where('is_active', 1)
                    ->orderBy('created_at','desc')
                    ->first();
        } else {
            return false;
        }
    }

    /**
     * Проверяем валидность данных (телефон, пин-код и новый пароль) и меняем пароль у пользователя по номеру телефона
     *
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(PasswordResetRequest $request)
    {
        self::$userPhone = $request->phone;

        $this->checkActiveEntries();

        if (!empty($this->getLastActiveEntry())) {
            $user = User::where('phone', $request->phone)->first();

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            $user->save();

            DB::table('sent_pin')
                ->where('phone', $request->phone)
                ->where('is_active', 1)
                ->update(['is_active' => 0]);

            //todo add to password_resets

            return redirect()->route('login', ['password_reset_success' => 'Пароль успешно изменен!']);
        }

        return redirect()->route('password.request', ['failed' => 'Произошла ошибка, попробуйте еще раз']);
    }
}
