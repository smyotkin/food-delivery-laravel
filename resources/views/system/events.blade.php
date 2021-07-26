<x-app-layout>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Настройки</h5>
    </x-slot>

    @include('layouts.settings-navigation')

    <div class="container-fluid px-5 mb-5">
        <div class="row">
            <div class="col-5 mt-4">
                <input type="text" id="label_msg-search" class="form-control rounded-0" placeholder="Поиск по метке или сообщению" aria-label="Поиск по метке или сообщению">
            </div>
            <div class="col-auto mt-4 d-flex align-items-center">
                <div class="spinner-border text-secondary d-none" role="status" id="preloader">
                    <span class="sr-only">Загрузка...</span>
                </div>
            </div>
        </div>
        <div class="row mt-5 mb-3">
            <div class="col-auto lh-1">
                <h5 class="d-inline-block fw-normal align-middle m-0">
                    Системные события
                    <a href="{{ '/system/events/export.csv' }}" class="btn btn-sm btn-danger align-bottom rounded-0 px-1 py-0 ms-2" id="download_csv"><small>CSV</small></a>
                </h5>
            </div>

            @php ($eventsCount = \App\Models\SystemEvents::count())

            @if ($eventsCount > 0)
                <div class="col text-end">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Очистить события
                            <span class="badge rounded-pill bg-danger ms-1">
                                {{ $eventsCount }}
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item clear_events" href="{{ route('system/events/clear', ['period' => 'day']) }}">За день</a></li>
                            <li><a class="dropdown-item clear_events" href="{{ route('system/events/clear', ['period' => 'week']) }}">За неделю</a></li>
                            <li><a class="dropdown-item clear_events" href="{{ route('system/events/clear', ['period' => 'month']) }}">За месяц</a></li>
                            <li><a class="dropdown-item clear_events" href="{{ route('system/events/clear', ['period' => 'year']) }}">За год</a></li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-12">
                <table class="table table-striped">
                    <thead>
                        <tr class="fw-light bg-lightgray table-header">
                            <td class="border-0">Дата создания</td>
                            <td class="border-0">Метка</td>
                            <td class="border-0">Пользователь</td>
                            <td class="border-0">Сообщение</td>
                        </tr>
                    </thead>
                    <tbody id="events_ajax"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let search = $('#label_msg-search');
        let ajaxBlock = $('#events_ajax');
        let getSearchCookie = getCookie('events_query_str');
        let page = 1;
        let searchNow = false;

        $(document).ready(function() {
            if (getSearchCookie)
                search.val(getSearchCookie);

            showList();

            search.on('keyup', function () {
                document.cookie = 'events_query_str=' + encodeURIComponent($(this).val());
                if ($(this).val().length >= 0) {
                    searchNow = true;
                    showList();
                } else {
                    searchNow = false;
                }
            });

        });

        function showList(page = 1) {
            let query = search.val();

            $('#download_csv').attr('href', '{{ '/system/events/export.csv' }}' + '?query=' + query);

            $.ajax({
                type: 'GET',
                data: {
                    query: query,
                },
                url: '{{ route('system/events/get.ajax') }}?page=' + page,
                beforeSend: function () {
                    $('#preloader').removeClass('d-none');
                },
                complete: function() {
                    $('#preloader').addClass('d-none');
                },
                success: function (data) {
                    $('.table_pagination', ajaxBlock).remove();

                    if (searchNow || page == 1) {
                        ajaxBlock.html(data);
                    } else {
                        ajaxBlock.append(data);
                    }
                }
            });
        }

        $('body').on('click', '.table_pagination a', function(event) {
            event.preventDefault();

            searchNow = false;
            page = $(this).attr('href').split('page=')[1];

            showList(page);
        });

        $('body').on('click', '.clear_events', function(event) {
            event.preventDefault();

            let route = $(this).attr('href');
            let url = new URL(route);
            let period = $(this).text().toLowerCase();
            let routeParams = new URLSearchParams(url.search);

            Swal.fire({
                title: 'Вы уверены?',
                text: 'Будут удалены все записи ' + period,
                icon: 'warning',
                confirmButtonText: 'Да, я уверен!',
                cancelButtonText: 'Отмена',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: route,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            period: routeParams.get('period'),
                        },
                        success: function (data) {
                            if (JSON.parse(data).success) {
                                showList();
                            }

                            if (JSON.parse(data).count) {
                                Swal.fire({
                                    title: 'Успешно',
                                    text: 'Всего было удалено: ' + JSON.parse(data).count,
                                    icon: 'info',
                                    showConfirmButton: false,
                                });
                            }
                        },
                        error: function (response) {
                            if (response.status === 404) {
                                Swal.fire({
                                    title: 'Внимание!',
                                    text: response.responseJSON.message,
                                    icon: 'warning',
                                    cancelButtonText: 'Закрыть',
                                    showCancelButton: true,
                                    showConfirmButton: false,
                                });
                            }
                        }
                    });
                }
            });
        })
    </script>
</x-app-layout>
