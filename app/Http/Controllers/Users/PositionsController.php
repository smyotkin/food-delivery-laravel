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
     * Настройка доступа через Middleware
     */
    public function __construct()
    {
        $this->middleware('permissions:users_position_delete')->only([
            'destroy',
        ]);
    }

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
        return view('users/position', [
            'statuses' => PositionsService::statuses,
            'permissions' => Permission::orderBy('group', 'desc')->get(),
        ])->render();
    }

    /**
     * Создание новой должности
     *
     * @param CreateOrUpdatePositionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     * @throws \Throwable
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
        $role = PositionsService::getWithPermissions(['id' => $id]);

        return view('users/position', [
            'role' => $role,
            'role_permissions' => !empty($role) ? $role->permissions->pluck('slug')->toArray() : [],
            'statuses' => PositionsService::statuses,
            'permissions' => Permission::orderBy('group', 'desc')->get(),
        ])->render();
    }

    /**
     * Редактирование\обновление должности
     *
     * @param CreateOrUpdatePositionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        PositionsService::destroy($id);

        return redirect()->route('positions.index');
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
            'statuses' => PositionsService::statuses,
        ])->render();
    }

    /**
     * Возвращает список должностей в <select>, для AJAX
     *
     * @param Request $request
     * @return string
     */
    public function getAjaxByStatus(Request $request): string
    {
        return view('users/positions-select', [
            'positions' => PositionsService::find($request->toArray()),
        ])->render();
    }

    /**
     * Возвращает Должность с правами в таблице, для AJAX
     *
     * @param Request $request
     * @return string
     */
    public function getRolePermissionsAjax(Request $request): string
    {
        return view('users/permissions-table', [
            'data' => PositionsService::getWithPermissions($request->input()),
        ])->render();
    }

    /**
     * Возвращает Должность с правами в таблице
     *
     * @param Request $request
     * @return string
     */
    public function getWithPermissions(Request $request): string
    {
        $role = PositionsService::getWithPermissions($request->input());

        return view('users/permissions-table', [
            'permissions' => Permission::orderBy('group', 'desc')->get(),
            'role_permissions' => $role->permissions->pluck('slug')->toArray(),
        ])->render();
    }
}
