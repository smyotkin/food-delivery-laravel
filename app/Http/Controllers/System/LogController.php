<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Services\SystemService;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        return view('system/log')->render();
    }

    /**
     * Получить список событий через Ajax
     *
     * @param Request $request
     * @return string
     */
    public function getEventsAjax(Request $request)
    {
        return view('system/log-block', [
            'data' => SystemService::getLog(),
        ])->render();
    }
}
