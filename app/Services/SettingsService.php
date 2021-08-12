<?php

namespace App\Services;

use App\Models\Settings;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingsService
{
    /**
     * Метод поиска настроек
     *
     * @param array|null $array
     * @param bool       $paginate
     * @return mixed
     */
    public static function find(?array $array = null, bool $paginate = true)
    {
        $settings = Settings::query()
            ->when(isset($array['query']), function ($query) use ($array) {
                $query
                    ->where('name', 'like', '%' . $array['query'] . '%')
                    ->orWhere('value', 'like', '%' . $array['query'] . '%');
            })
            ->orderBy('key', 'asc');

        return $paginate ? $settings->simplePaginate(Settings::get('global_rows_per_page')) : $settings->get();
    }

    /**
     * Функция возвращает расширенные параметры настройки
     *
     * @param $key
     * @param $value
     * @return array|bool
     */
    public static function getSetting($key, $value)
    {
        switch ($key) {
            case 'global_event_period':
                return [
                    'name' => 'Период хранения системных событий',
                    'value' => $value,
                    'raw' => $value,
                    'data' => [
                        'day' => 'День',
                        'week' => 'Неделя',
                        'month' => 'Месяц',
                        'year' => 'Год',
                    ],
                    'type' => 'select',
                    'default' => 'month',
                ];
            case 'global_rows_per_page':
                return [
                    'name' => 'Количество записей на странице',
                    'value' => $value,
                    'raw' => $value,
                    'type' => 'number',
                    'default' => '50',
                ];
            case 'global_max_rows_limit':
                return [
                    'name' => 'Максимальный лимит записей',
                    'value' => $value,
                    'raw' => $value,
                    'type' => 'number',
                    'default' => '1000',
                ];
            case 'smscru_login':
                return [
                    'name' => 'Логин СМС-центра',
                    'value' => $value,
                    'raw' => $value,
                    'type' => 'text',
                    'default' => '',
                ];
            case 'smscru_secret':
                return [
                    'name' => 'Пароль СМС-центра',
                    'value' => !empty($value) ? Crypt::decryptString($value) : $value,
                    'raw' => $value,
                    'type' => 'text',
                    'default' => '',
                    'encryption' => true,
                ];
            case 'telegram_token':
                return [
                    'name' => 'Токен Telegram-бота',
                    'value' => !empty($value) ? Crypt::decryptString($value) : $value,
                    'raw' => $value,
                    'type' => 'text',
                    'default' => '',
                    'encryption' => true,
                ];
        }

        return false;
    }

    /**
     * Форматирование настроек
     *
     * @param $array
     * @return array|bool
     */
    public static function formatSetting($array)
    {
        if (!empty($array)) {
            $formatted = [];

            foreach ($array as $row) {
                if ($formattedValue = self::getSetting($row['key'], $row['value'])) {
                    $formatted[] = $formattedValue + [
                        'key' => $row['key'],
                    ];
                }
            }

            return $formatted;
        }

        return false;
    }

    /**
     * Обновление одного параметра настроек
     *
     * @param array|null $array
     * @throws \Throwable
     */
    public static function update(?array $array = null)
    {
        $basicParams = collect($array)
            ->only([
                'global_event_period',
                'global_rows_per_page',
                'global_max_rows_limit',
                'smscru_login',
                'smscru_secret',
                'telegram_token',
            ]);

        $validator = Validator::make($array, [
            'global_event_period' => 'in:day,week,month,year',
            'global_rows_per_page' => 'numeric',
            'global_max_rows_limit' => 'numeric',
            'smscru_login' => '',
            'smscru_secret' => '',
            'telegram_token' => '',
        ]);

        if ($validator->fails()) {
            SystemService::createEvent('setting_update_failed', Settings::find($basicParams->keys()->first())
                ->toArray(), $validator->errors()->toArray() + [
                    'value' => $basicParams->first(),
                ]);

            abort(500);
        }

        try {
            DB::transaction(function() use ($basicParams) {
                if ($key = $basicParams->keys()->first()) {
                    if (in_array($key, ['smscru_secret', 'telegram_token'])) {
                        $value = Crypt::encryptString($basicParams->first());
                    } else {
                        $value = $basicParams->first();
                    }

                    $setting = Settings::find($key);
                    $oldValue = $setting->value;
                    $updated = $setting->update([
                        'value' => $value,
                    ]);

                    if ($updated) {
                        SystemService::createEvent('setting_updated', $setting->toArray(), [
                            'old_value' => $oldValue,
                            'new_value' => $setting->value,
                        ]);
                    }
                }
            });
        } catch (\Exception $e) {
            SystemService::createEvent('setting_update_failed', Settings::find($basicParams->keys()->first())
                ->toArray(), [
                    'value' => $basicParams->first(),
                ]);

            abort(500);
        }
    }

    /**
     * Очистка кэша
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function clearCache()
    {
        \Artisan::call('optimize:clear');
        return redirect()->back()->with('status', 'Cache Cleared!');
    }
}
