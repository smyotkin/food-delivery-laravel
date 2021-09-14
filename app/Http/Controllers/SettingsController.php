<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;
use Illuminate\Http\Request;
use Throwable;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index(): string
    {
        return view('settings/settings')->render();
    }

    /**
     * Возвращает список пользователей в таблице, для AJAX
     *
     * @param Request $request
     * @return string
     */
    public function getAjax(Request $request): string
    {
        $allSettings = SettingsService::find($request->toArray());

        return view('settings/settings-table', [
            'settings' => $allSettings,
            'formatted' => SettingsService::formatSetting($allSettings->toArray()['data']),
        ])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @throws Throwable
     */
    public function update(Request $request): void
    {
        SettingsService::update($request->all());
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        SettingsService::clearCache();
    }
}
