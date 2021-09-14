<?php

namespace App\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Settings;
use Exception;
use Throwable;
use Log;

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

        return $paginate ? $settings->simplePaginate() : $settings->get();
    }

    /**
     * Функция возвращает расширенные параметры настройки
     *
     * @param $key
     * @param $value
     * @return array|null
     */
    public static function getSetting(string $key, string $value): ?array
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
                    'min' => '1',
                    'max' => '1000',
                ];
            case 'global_max_rows_limit':
                return [
                    'name' => 'Максимальный лимит записей',
                    'value' => $value,
                    'raw' => $value,
                    'type' => 'number',
                    'default' => '1000',
                    'min' => '1',
                    'max' => '1000',
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

        return null;
    }

    /**
     * Форматирование настроек
     *
     * @param $array
     * @return array
     */
    public static function formatSetting(array $array): array
    {
        if (empty($array)) {
            return [];
        }

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

    /**
     * Обновление одного параметра настроек
     *
     * @param array|null $array
     * @throws Exception|Throwable
     */
    public static function update(?array $array = null): void
    {
        $params = collect($array)->only([
            'global_event_period',
            'global_rows_per_page',
            'global_max_rows_limit',
            'smscru_login',
            'smscru_secret',
            'telegram_token',
        ]);

        $validator = Validator::make($params->all(), [
            'global_event_period' => 'in:day,week,month,year',
            'global_rows_per_page' => 'numeric|min:1|max:1000',
            'global_max_rows_limit' => 'numeric|min:1|max:1000',
        ]);

        $key = $params->keys()->first();
        $value = $params->first();
        $beforeUpdate = $setting = Settings::findOrFail($key);

        if ($validator->fails()) {
            SystemService::createEvent(
                'setting_update_failed',
                $beforeUpdate->toArray(),
                $validator->errors()->toArray() + [
                    'value' => $value,
                ]
            );

            throw new ValidationException($validator);
        }

        if ($key && $value) {
            if (in_array($key, ['smscru_secret', 'telegram_token'])) {
                $value = Crypt::encryptString($value);
            }

            try {
                $setting->update(['value' => $value]);
                $setting->saveOrFail();
            } catch (Throwable $error) {
                SystemService::createEvent('setting_update_failed', $setting->toArray(), [
                    'value' => $value,
                    'error' => $error,
                ]);

                Log::error($error);

                abort(500, 'Произошла ошибка при обновлении');
            }

            SystemService::createEvent('setting_updated', $setting->toArray(), [
                'old_value' => $beforeUpdate->value,
                'new_value' => $value,
            ]);
        }
    }

    /**
     * Очистка кэша
     *
     * @return RedirectResponse
     */
    public static function clearCache(): RedirectResponse
    {
        \Artisan::call('optimize:clear');
        return redirect()->back()->with('status', 'Cache Cleared!');
    }
}
