<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\PositionsService;
use Illuminate\Http\Request;
use App\Http\Requests\Users\CreateOrUpdatePositionRequest;
use App\Models\Permission;

class PositionsController extends Controller
{
    /**
     * Отображение таблицы всех должностей
     *
     * @return string
     */
    public function index(): string
    {
        return view('users/positions')->render();
    }

    /**
     * Шаблон (Форма) создания новой должности
     *
     * @return string
     */
    public function create(): string
    {
        return view('users/position')->render();
    }

    /**
     * Создание новой должности
     *
     * @param CreateOrUpdatePositionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(CreateOrUpdatePositionRequest $request): \Illuminate\Http\RedirectResponse
    {
        PositionsService::createOrUpdate($request->validated());

        return redirect()->route('positions.index');
    }

    /**
     * Просмотр одной должности (по id)
     *
     * @param $id
     * @return string
     */
    public function show($id): string
    {
        $position = PositionsService::get(['id' => $id]);
        $statusPermissions = PositionsService::getStatusPermissions($position->status);

        return view('users/position', [
            'role' => $position,
            'permissions' => Permission::statusPermissionsToTop($statusPermissions),
            'status_permissions' => $statusPermissions,
        ])->render();
    }

    /**
     * Редактирование\обновление должности
     *
     * @param CreateOrUpdatePositionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(CreateOrUpdatePositionRequest $request): \Illuminate\Http\RedirectResponse
    {
        PositionsService::createOrUpdate($request->validated());

        return redirect()->route('positions.index');
    }

    /**
     * Удаление должности
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Возвращает список должностей в таблице, для AJAX
     *
     * @param Request $request
     * @return string
     */
    public function getAjax(Request $request): string
    {
        return view('users/positions-table', [
            'data' => PositionsService::find($request->toArray()),
        ])->render();
    }
}
