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
     * @param Request $request
     */
    public function update(Request $request)
    {

    }
}
