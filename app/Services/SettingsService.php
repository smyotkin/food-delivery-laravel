<?php

namespace App\Services;

use App\Models\Settings;
use Illuminate\Support\Facades\Validator;
use Exception;

class SettingsService
{
    /**
     * Метод поиска настроек
     *
     * @param array|null $array
     * @return mixed
     */
    public static function find(?array $array = null)
    {
        $settings = Settings::query()
            ->when(isset($array['query']), function ($query) use ($array) {
                $query
                    ->where('key', 'like', '%' . $array['query'] . '%')
                    ->orWhere('value', 'like', '%' . $array['query'] . '%');
            })
            ->orderBy('key', 'asc');

        return $settings->simplePaginate();
    }
}
