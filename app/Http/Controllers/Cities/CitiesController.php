<?php

namespace App\Http\Controllers\Cities;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cities\CreateOrUpdateCityRequest;
use App\Services\CitiesService;
use App\Services\UsersService;
use Exception;
use Fomvasss\Dadata\Facades\DadataSuggest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CitiesController extends Controller
{
    /**
     * Отображает список городов
     *
     * @return string
     */
    public function index(): string
    {
        return view('cities/cities')->render();
    }

    /**
     * Возвращает список городов в таблице, для AJAX
     *
     * @param Request $request
     * @return string
     */
    public function getAjax(Request $request): string
    {
        return view('cities/cities-table', [
            'data' => CitiesService::find($request->toArray()),
        ])->render();
    }

    /**
     * Просмотр одного города (по id)
     *
     * @param int $id
     * @return string
     * @throws Throwable
     */
    public function show(int $id): string
    {
        return view('cities/city', [
            'city' => CitiesService::getOrFail($id),
        ])->render();
    }

    /**
     * Возвращает форму города (принимает $action - show (все данные) или create (пусто)), для AJAX
     *
     * @param Request $request
     * @return string
     */
    public function getFormAjax(Request $request): string
    {
        $returnedData = [
            'timezones' => UsersService::timezones,
            'time_shift' => CitiesService::timeShift,
        ];

        if ($request->action == 'show') {
            $kladr_cities = CitiesService::getKladrByCityId($request->id);

            $returnedData = $returnedData + [
                'city' => CitiesService::getOrFail($request->id),
                'kladr_cities_json' => $kladr_cities->value ?? '',
                'kladr_cities' => $kladr_cities->json_decoded_value ?? [],
            ];
        }

        return view('cities/city-form', $returnedData)->render();
    }

    /**
     * Поиск городов DADATA, для AJAX
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchCitiesAjax(Request $request): JsonResponse
    {
        //todo validate

        return response()->json(DadataSuggest::suggest('address', [
            'query' => $request->query('query'),
            'count' => 10,
            "from_bound" => [
                "value" => "city"
            ],
            "to_bound" => [
                "value" => "settlement"
            ],
        ]));
    }

    /**
     * Шаблон добавления города
     *
     * @return string
     */
    public function create(): string
    {
        return view('cities/city')->render();
    }

    /**
     * Редактирование города
     *
     * @param CreateOrUpdateCityRequest $request
     * @throws Exception|Throwable
     */
    public function update(CreateOrUpdateCityRequest $request): void
    {
        CitiesService::createOrUpdate($request->validated());
    }

    /**
     * Создание города
     *
     * @param CreateOrUpdateCityRequest $request
     * @throws Exception|Throwable
     */
    public function store(CreateOrUpdateCityRequest $request): void
    {
        CitiesService::createOrUpdate($request->validated());
    }
}
