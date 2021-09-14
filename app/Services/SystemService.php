<?php

namespace App\Services;

use App\Models\EventsNotifications;
use App\Models\Settings;
use App\Models\SystemEvents;
use App\Notifications\Telegram;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Throwable;

class SystemService
{
    /**
     * Метод поиска событий
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

        return $paginate ? $settings->simplePaginate() : $settings->get();
    }

    /**
     * Метод создания нового события
     *
     * @param string     $slug
     * @param array|null $element
     * @param array|null $data
     * @throws Throwable
     */
    public static function createEvent(string $slug, ?array $element = null, ?array $data = null): void
    {
        self::clearExpiredEvents();

        if ($event = EventsNotifications::find($slug)) {
            $msg = self::replacePlaceholders($event['msg_template'], $element);

            $createEvent = SystemEvents::create([
                'slug' => $slug,
                'label' => $event->label,
                'msg' => $msg,
                'data' => $data ? json_encode($data ?? $element, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) : null,
                'user_id' => Auth::user()->id ?? 0,
                'created_at' => Carbon::now(),
            ]);

            self::sendEventNotification($slug, $msg, $createEvent);
        }
    }

    /**
     * Метод отправки уведомления о произошедшем событии
     *
     * @param string            $slug
     * @param string            $msg
     * @param SystemEvents|null $event
     */
    public static function sendEventNotification(string $slug, string $msg, ?SystemEvents $event = null): void
    {
        $eventNotification = EventsNotifications::find($slug);
        $chatIds = explode(',', str_replace([' '], '', $eventNotification->recipient_ids));

        if (!empty($eventNotification->recipient_ids) && $chatIds) {
            $msgParams = [
                'date' => !empty($event->date) ? $event->date : '',
                'ip' => !empty($event->ip) ? "\nIP: {$event->ip}" : '',
                'auth_user' => "\nПользователь: " . (!empty($event->user->full_name) ? $event->user->full_name : 'Неавторизован'),
            ];

            foreach ($chatIds as $user) {
                Notification::route('telegram', $user)
                    ->notify(new Telegram([
                        'msg' => $msgParams['date'] . ' - ' . $msg . $msgParams['auth_user'] . $msgParams['ip'],
                    ]));
            }
        }
    }

    /**
     * Метод замены плейсхолдеров (напр. {test}) в тексте
     *
     * @param string     $msg
     * @param array|null $element
     * @return string
     */
    public static function replacePlaceholders(string $msg, ?array $element): string
    {
        $replaces = collect($element)
            ->only([
                'name',
                'slug',
                'full_name',
                'phone',
                'ip',
            ])
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
     * @return int
     */
    public static function clearEvents(string $period): int
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

        return (int) SystemEvents::whereRaw("DATE(created_at) BETWEEN '$date_start' AND '$date_end'")->delete();
    }

    /**
     * Удаляет события старше установленного срока в настройках
     *
     * @throws Throwable
     */
    public static function clearExpiredEvents(): void
    {
        $period = strtoupper(Settings::get('global_event_period'));
        $number = $period == 'DAY' ? 0 : 1;

        SystemEvents::whereRaw("created_at < DATE_SUB(CURDATE(), INTERVAL $number $period)")->delete();
    }

    /**
     * Обновление настройки уведомления события
     *
     * @param array|null $array
     * @throws Throwable
     */
    public static function updateNotificationOrFail(?array $array = null): void
    {
        $params = collect($array)->only([
            'key',
            'msg_template',
            'recipient_ids',
        ]);
        $notification = EventsNotifications::getOrFail($params->get('key'));

        $notification->update($params->all());
        $notification->saveOrFail();
    }
}
