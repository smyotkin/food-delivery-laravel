<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Exports\SystemEventsExport;
use App\Services\SystemService;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    /**
     * Список всех событий
     *
     * @return string
     */
    public function index()
    {
        return view('system/events')->render();
    }

    /**
     * Удаление событий за период
     *
     * @param Request $request
     * @return false|string
     */
    public function clearEvents(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|in:day,week,month,year',
        ]);

        $count = SystemService::clearEvents($validated['period']);

        return json_encode([
            'success' => true,
            'count' => $count,
        ], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
    }

    /**
     * Экспорт в CSV
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportEventsCsv(Request $request)
    {
        return (new SystemEventsExport(SystemService::findEvents($request->toArray(), false)))->download();
    }

    /**
     * Получить список событий через Ajax
     *
     * @param Request $request
     * @return string
     */
    public function getEventsAjax(Request $request)
    {
        return view('system/events-table', [
            'data' => SystemService::findEvents($request->toArray()),
        ])->render();
    }
}
