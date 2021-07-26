<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Services\SystemService;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    //
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
    public function getEventsAjax(Request $request)
    {
        return view('system/notifications-table', [
            'data' => SystemService::events,
        ])->render();
    }
}
