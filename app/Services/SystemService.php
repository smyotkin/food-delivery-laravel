<?php

namespace App\Services;

use App\Models\EventsNotifications;
use App\Models\Settings;
use App\Models\SystemEvents;
use App\Notifications\Telegram;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SystemService
{
    /**
     * Список событий
     */
    public const events = [
        'position_remove_success' => [
            'label' => 'Удаление должности',
            'msg_template' => 'Должность "{name}" была удалена',
        ],
        'position_remove_error' => [
            'label' => 'Удаление должности',
            'msg_template' => 'Попытка удалить должность "{name}" не удалась.',
        ],
    ];

    /**
     * Метод поиска событий
     *
     * @param array|null $array
     * @param bool       $paginate
     * @return mixed
     */
    public static function findEvents(?array $array = null, bool $paginate = true)
    {
        self::clearExpiredEvents();

        $settings = SystemEvents::query()
            ->when(isset($array['query']), function ($query) use ($array) {
                $query
                    ->where('label', 'like', '%' . $array['query'] . '%')
                    ->orWhere('msg', 'like', '%' . $array['query'] . '%');
            })
            ->orderBy('created_at', 'desc');

        return $paginate ? $settings->simplePaginate(Settings::get('global_rows_per_page')) : $settings->get();
    }

    /**
     * Метод поиска уведомлений
     *
     * @param array|null $array
     * @param bool       $paginate
     * @return mixed
     */
    public static function findEventsNotifications(?array $array = null, bool $paginate = true)
    {
        $settings = EventsNotifications::query()
            ->when(isset($array['query']), function ($query) use ($array) {
                $query
                    ->where('label', 'like', '%' . $array['query'] . '%')
                    ->orWhere('msg_template', 'like', '%' . $array['query'] . '%');
            })
            ->orderBy('label', 'asc');

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
        self::clearExpiredEvents();

        if ($event = self::events[$slug]) {
            $msg = self::replacePlaceholders($event['msg_template'], $element);

            self::sendEventNotification($slug, $msg);

            return SystemEvents::create([
                'slug' => $slug,
                'label' => $event['label'],
                'msg' => $msg,
                'data' => $data ? json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) : null,
                'user_id' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
        }

        return false;
    }

    /**
     * Метод отправки уведомления о произошедшем событии
     *
     * @param $slug
     * @param $msg
     */
    public static function sendEventNotification($slug, $msg) {
        $eventNotification = EventsNotifications::find($slug)->first();
        $chatIds = explode(',', str_replace([' '], '', $eventNotification->recipient_ids));

        foreach($chatIds as $user) {
            Notification::route('telegram', $user)
                ->notify(new Telegram([
                    'msg' => $msg,
                ]));
        }
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
     * Очистка за переданный период
     *
     * @param string $period
     * @return mixed
     */
    public static function clearEvents(string $period)
    {
        $date_start = $date_end = Carbon::now()->toDateString();

        switch ($period) {
            case 'week':
                $date_start = Carbon::now()->subWeek()->toDateString();
                break;
            case 'month':
                $date_start = Carbon::now()->subMonth()->toDateString();
                break;
            case 'year':
                $date_start = Carbon::now()->subYear()->toDateString();
                break;
        }

        $delete = SystemEvents::whereRaw("DATE(created_at) BETWEEN '$date_start' AND '$date_end'")->delete();

        return $delete ? $delete : abort(404, 'Записи не найдены');
    }

    /**
     * Удаляет события старше установленного срока в настройках
     *
     * @return mixed
     */
    public static function clearExpiredEvents()
    {
        $period = strtoupper(Settings::get('global_event_period'));
        $number = $period == 'DAY' ? 0 : 1;
        $delete = SystemEvents::whereRaw("created_at < DATE_SUB(CURDATE(), INTERVAL $number $period)")
            ->delete();

        return $delete ? $delete : false;
    }

    /**
     * Обновление настройки уведомления события
     *
     * @param array|null $array
     * @throws \Throwable
     */
    public static function updateNotification(?array $array = null): void
    {
        $basicParams = collect($array)
            ->only(['msg_template', 'recipient_ids']);

        try {
            DB::transaction(function() use ($basicParams, $array) {
                if ($notification = EventsNotifications::get($array['key'])) {
                    $notification->update($basicParams->all());
                    $notification->saveOrFail();

                    Log::info("UPDATE_NOTIFICATION: id({$notification->key})");
                } else {
                    return false;
                }
            });
        } catch (\Exception $e) {
            Log::info("UPDATE_NOTIFICATION_ERROR: {$e->getMessage()}");
            abort(500);
        }
    }
}
