<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Requests\Users\CreateOrUpdatePositionRequest;
use App\Http\Controllers\Controller;
use App\Services\PositionsService;
use App\Models\Permission;
use Exception;
use Throwable;

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
     * @throws Exception|Throwable
     */
    public function store(CreateOrUpdatePositionRequest $request): void
    {
        PositionsService::createOrUpdate($request->validated());
    }

    /**
     * Просмотр одной должности (по id)
     *
     * @param int $id
     * @return string
     */
    public function show(int $id): string
    {
        $role = PositionsService::getWithPermissionsOrFail(['id' => $id]);

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
     * @throws Exception|Throwable
     */
    public function update(CreateOrUpdatePositionRequest $request): void
    {
        PositionsService::createOrUpdate($request->validated());
    }

    /**
     * Удаление должности
     *
     * @param int $id
     * @throws Exception|Throwable
     */
    public function destroy($id): void
    {
        PositionsService::destroy($id);
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
    public function getSelectAjax(Request $request): string
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
     * Возвращает форму должности(принимает $action - show(все данные) или create(пусто)), для AJAX
     *
     * @param Request $request
     * @return string
     */
    public function getFormAjax(Request $request): string
    {
        $returnedData = [
            'statuses' => PositionsService::statuses,
            'permissions' => Permission::orderBy('group', 'desc')->get(),
        ];

        if ($request->action == 'show') {
            $role = PositionsService::getWithPermissions(['id' => $request->id]);
            $returnedData = $returnedData + [
                'role' => $role,
                'role_permissions' => !empty($role) ? $role->permissions->pluck('slug')->toArray() : [],
            ];
        }

        return view('users/position-form', $returnedData)->render();
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
