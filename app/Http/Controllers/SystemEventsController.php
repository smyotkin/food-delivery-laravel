<?php

namespace App\Http\Controllers;

use App\Exports\SystemEventsExport;
use App\Services\SystemService;
use Illuminate\Http\Request;

class SystemEventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        return view('system/events')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Экспорт в CSV
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportEventsCsv(Request $request)
    {
        return (new SystemEventsExport(SystemService::findEvents($request->toArray())))->download();
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
