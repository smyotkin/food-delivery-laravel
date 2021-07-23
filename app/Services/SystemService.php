<?php

namespace App\Services;

use App\Models\Settings;
use App\Models\SystemEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SystemService
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
        $settings = SystemEvents::query()
            ->when(isset($array['query']), function ($query) use ($array) {
                $query
                    ->where('name', 'like', '%' . $array['query'] . '%');
//                    ->orWhere('value', 'like', '%' . $array['query'] . '%');
            })
            ->orderBy('id', 'asc');

        return $paginate ? $settings->simplePaginate(Settings::get('global_rows_per_page')) : $settings->get();
    }
}
