<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Exports\SystemEventsExport;
use App\Services\SystemService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
     * @return mixed
     */
    public function clearEvents(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|in:day,week,month,year',
        ]);

        return response([
            'count' => SystemService::clearEvents($validated['period']),
        ]);
    }

    /**
     * Экспорт в CSV
     *
     * @param Request $request
     * @return Response|BinaryFileResponse
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
