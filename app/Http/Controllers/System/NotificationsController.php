<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Services\SystemService;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
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
     * @return false|string
     * @throws \Throwable
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required',
            'msg_template' => 'required',
            'recipient_ids' => '',
        ]);

        SystemService::updateNotification($validated);

        return json_encode([
            'success' => true,
        ], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
    }
}
