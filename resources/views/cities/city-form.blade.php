<div class="row">
    <div class="col">
        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
    </div>
</div>

<div class="row">
    <form method="post" action="{{ isset($city) ? route('cities.update', ['city' => $city]) : route('cities.store') }}" id="cities_form" class="col-12 col-md-10 update_city needs-validation" novalidate>
        @method(isset($city) ? 'patch' : 'post')
        @csrf

        <fieldset class="row g-3" {{ isset($city) && !auth()->user()->hasPermission('cities_modify') ? 'disabled' : '' }}>
            <div class="col-12 col-md-5">
                <div class="row g-3">
                    @if (isset($city))
                        <input type="hidden" name="id" value="{{ $city->id }}">
                    @endif

                    <div class="col-12">
                        <label for="name" class="form-label fw-bold">Название</label>
                        <input type="text" class="form-control rounded-0" id="name" name="name" value="{{ $city->name ?? '' }}" placeholder="Название" required>
                        <div class="invalid-feedback text-sm">
                            Поле обязательное, не менее 2 симв.
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="phone" class="form-label fw-bold">Телефон колл-центра</label>
                        <input type="text" class="form-control rounded-0" id="phone" name="phone" placeholder="Телефон колл-центра" aria-label="Телефон колл-центра" aria-describedby="country_code" value="{{ $city->phone ?? '' }}">

                        <div class="invalid-feedback text-sm">
                            Поле обязательное, не менее 2 симв.
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="folder" class="form-label fw-bold">Каталог</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-0" id="site_link">{{ 'https://' . config('custom.subdomain.site') . '/' }}</span>
                            <input type="text" class="form-control rounded-0" id="folder" name="folder" placeholder="Каталог" aria-label="Каталог" aria-describedby="site_link" value="{{ $city->folder ?? '' }}">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" id="multicode" name="multicode" {{ !empty($city->multicode) || !isset($city) ? 'checked' : '' }}>
                            <label class="form-check-label" for="multicode">Несколько кодов в одном городе</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ !empty($city->is_active) || !isset($city) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Город активен</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-7">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="timezone" class="form-label fw-bold">Часовой пояс</label>
                        <select class="form-select rounded-0" id="timezone" name="timezone" required>
                            <option disabled selected>Ничего не выбрано</option>
                            @foreach ($timezones as $key => $timezone)
                                <option value="{{ $key }}" {{ isset($city->timezone) && $city->timezone == $key ? 'selected' : '' }}>{{ $timezone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="work_hours_shift" class="form-label fw-bold">Смещение времени закрытия</label>
                        <select class="form-select rounded-0" id="work_hours_shift" name="work_hours_shift" required>
                            <option disabled selected>Ничего не выбрано</option>
                            @foreach ($time_shift as $number)
                                <option value="{{ $number }}" {{ isset($city->work_hours_shift) && $city->work_hours_shift == $number ? 'selected' : '' }}>{{ $number }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="working_hours" class="form-label fw-bold">Часы работы</label>

{{--                        <input name="working_hours" type="hidden" value="{{ $kladr_cities_json }}">--}}

                        <div class="table-responsive">
                            <table class="table table-sm table-sm-padding text-center align-middle" style="font-size: 10px">
                                <thead>
                                    <tr>
                                        <th></th>
                                        @for ($hour = 0; $hour <= 23; $hour++)
                                            <th>
                                                <a href="javascript:" class="text-decoration-none text-dark" onclick="$('.group-{{ $hour }}').prop('checked', !$('.group-{{ $hour }}').prop('checked'))">
                                                    {{ sprintf('%02d', $hour) }}
                                                </a>
                                            </th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($weekdays = [
                                        'mon' => 'ПН',
                                        'tue' => 'ВТ',
                                        'wed' => 'СР',
                                        'thu' => 'ЧТ',
                                        'fri' => 'ПТ',
                                        'sat' => 'СБ',
                                        'sun' => 'ВС',
                                    ])

                                    @foreach($weekdays as $key => $day)
                                        <tr>
                                            <th>
                                                <a href="javascript:" class="text-decoration-none text-dark" onclick="$('.group-{{ $key }}').prop('checked', !$('.group-{{ $key }}').prop('checked'))">{{ $day }}</a>
                                            </th>
                                            @for ($hour = 0; $hour <= 23; $hour++)
                                                <td>
                                                    <input class="form-check-input big-checkbox group-{{ $key }} group-{{ $hour }}" type="checkbox" name="working_hours[{{ $key }}][]" value="{{ $hour }}" {{ !empty($city) && in_array($hour, $city['work_hours_array'][$key]) ? 'checked' : '' }} data-row="{{ $loop->index }}" data-column="{{ $hour }}">
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12">
                        <input type="hidden" id="kladr_cities" name="kladr_cities" value="{{ $kladr_cities_json ?? '' }}">

                        <div class="form-group">
                            <label class="form-label fw-bold" for="kladr_cities_select">Подсказки адресов</label>

                            <select id="kladr_cities_select" class="form-control select2-multiple" data-toggle="select2" multiple="multiple">
                                @if (!empty($kladr_cities))
                                    @foreach($kladr_cities as $id => $city)
                                        <option value="{{ $id }}" title="{{ $city }}" selected>{{ $city }}</option>
                                    @endforeach
                                @endif
                            </select>

                            <span class="d-block mt-2 small text-muted">Выберите текущий город и список близлежащих к нему
                            населенных пунктов, по которым необходим поиск адресов</span>

                            <div class="invalid-feedback">
                                Выберите населенные пункты
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>

    @permission('cities_modify')
        <div class="d-block d-md-none">
            <p class="mb-2">
                <a href="javascript:" class="save_btn d-block d-md-inline-block btn btn-outline-secondary py-0 mt-3 mt-md-0 disabled">Сохранить</a>
            </p>
        </div>
    @endpermission
</div>

<script>
    // $('.big-checkbox').on('mousedown mouseover', function (e) {
    //     // console.log(e.type);
    //     let checkboxes = [];
    //
    //     if (e.buttons === 1 || e.buttons === 3) {
    //     // if (e.type === 'mousedown') { //  && e.type === 'mouseover'
    //         checkboxes.push($(this).data('row') + ':' + $(this).data('column'));
    //         // $(this).prop('checked', !$(this).prop('checked'));
    //         // console.log($(this).val());
    //         console.log($(this).data('row') + ':' + $(this).data('column'));
    //     }
    // });

    $('#kladr_cities_select').select2({
        minimumInputLength: 1,
        language: {
            loadingMore: function () {
                return "Загрузка данных…"
            },
            errorLoading: function () {
                return 'Ошибка поиска'
            },
            inputTooShort: function (e) {
                return 'Начните вводить название...'
            },
            noResults: function () {
                return "Ничего не найдено"
            },
            searching: function () {
                return "Поиск…"
            }
        },
        ajax: {
            type: 'GET',
            url: '{{ '/cities/search/get.ajax' }}',
            contentType: 'application/json',
            dataType: 'json',
            headers: {
                Accept: "application/json",
            },
            data: function (params) {
                return {
                    query: params.term
                };
            },
            processResults: function (data) {
                let items = [];

                console.log(data);

                for (let item in data) {
                    items.push({
                        id: data[item]['data']['kladr_id'],
                        title: data[item]['value'],
                        text: data[item]['value']
                    });
                }

                return {
                    results: items
                };
            },
        }
    }).on('change', function () {
        let items = {};

        $(this).find('option:selected').each(function () {
            items[$(this).val()] = $(this).attr('title');
        });

        $('#kladr_cities').val(JSON.stringify(items)).trigger('change');
    });
</script>
