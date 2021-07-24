<?php

namespace App\Services;

use App\Models\Settings;
use App\Models\SystemEvents;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SystemService
{
    /**
     * Список событий
     */
    public const events = [
        'position_remove_success' => [
            'label' => 'Должность удалена',
            'msg_template' => 'Должность "{name}" была удалена',
        ],
        'position_remove_error' => [
            'label' => 'Удаление должности',
            'msg_template' => 'Попытка удалить должность "{name}" не удалась.',
        ],
    ];

    /**
     * Метод поиска настроек
     *
     * @param array|null $array
     * @param bool       $paginate
     * @return mixed
     */
    public static function findEvents(?array $array = null, bool $paginate = true)
    {
        $settings = SystemEvents::query()
            ->when(isset($array['query']), function ($query) use ($array) {
                $query
                    ->where('label', 'like', '%' . $array['query'] . '%')
                    ->orWhere('msg', 'like', '%' . $array['query'] . '%');
            })
            ->orderBy('id', 'asc');

        return $paginate ? $settings->simplePaginate(Settings::get('global_rows_per_page')) : $settings->get();
    }

    /**
     * Метод создания нового события
     *
     * @param            $slug
     * @param array|null $element
     * @param array|null $data
     * @return bool
     */
    public static function createEvent($slug, ?array $element = null, ?array $data = null)
    {
        if ($event = self::events[$slug]) {
            return SystemEvents::create([
                'slug' => $slug,
                'label' => $event['label'],
                'msg' => self::replacePlaceholders($event['msg_template'], $element),
                'data' => $data ? json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
                    : null,
                'user_id' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
        }

        return false;
    }

    /**
     * Метод замены плейсхолдеров (напр. {test}) в тексте
     *
     * @param $msg
     * @param $element
     * @return string|string[]
     */
    public static function replacePlaceholders($msg, $element)
    {
        $replaces = collect($element)
            ->only(['name', 'slug'])
            ->all();

        foreach ($replaces as $key => $value) {
            $msg = str_replace('{' . $key . '}', $value, $msg);
        }

        return $msg;
    }

    /**
     * Очистка всех событий или за переданный период
     */
    public static function clearEvents()
    {
        //
    }
}
