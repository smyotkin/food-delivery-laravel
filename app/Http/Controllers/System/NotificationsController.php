<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Services\SystemService;
use Illuminate\Http\Request;
use Exception;
use Throwable;

class NotificationsController extends Controller
{
    /**
     * Отобразить страницу с событиями
     *
     * @return string
     */
    public function index()
    {
        return view('system/notifications')->render();
    }

    /**
     * Получить список событий через Ajax
     *
     * @param Request $request
     * @return string
     */
    public function getAjax(Request $request)
    {
        return view('system/notifications-table', [
            'data' => SystemService::findEventsNotifications($request->toArray()),
        ])->render();
    }

    /**
     * Метод обновления настройки события
     *
     * @param Request $request
     * @throws Exception|Throwable
     */
    public function update(Request $request): void
    {
        SystemService::updateNotificationOrFail($request->validate([
            'key' => 'required',
            'msg_template' => 'required',
            'recipient_ids' => '',
        ]));
    }
}
