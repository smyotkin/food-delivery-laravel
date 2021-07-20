<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Services\SettingsService;
use Illuminate\Http\Request;

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
     * @return false|string
     * @throws \Throwable
     */
    public function update(Request $request)
    {
        SettingsService::update($request->all());

        return json_encode([
            'success' => true,
        ], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
    }


    public function clearCache()
    {
        SettingsService::clearCache();

        return json_encode([
            'success' => true,
        ], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
    }
}
