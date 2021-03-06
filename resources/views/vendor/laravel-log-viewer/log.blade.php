<x-app-layout>
    <x-slot name="title">Лог ошибок</x-slot>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Настройки</h5>
    </x-slot>

    <style>
        #table-log {
            font-size: 0.85rem;
        }

        .sidebar {
            font-size: 0.85rem;
            line-height: 1;
        }

        .stack {
            font-size: 0.85em;
        }

        .date {
            min-width: 75px;
        }

        .text {
            word-break: break-all;
        }

        a.llv-active {
            z-index: 2;
            background-color: #f5f5f5;
            border-color: #777;
        }

        .list-group-item {
            word-break: break-word;
        }

        .folder {
            padding-top: 15px;
        }

        .div-scroll {
            /*height: 80vh;*/
            overflow: hidden auto;
        }

        .nowrap {
            white-space: nowrap;
        }
    </style>

    @include('layouts.settings-navigation')

    <div class="container-fluid px-4 px-md-5 mb-5">
        <div class="row mt-md-5 mt-4 mb-3">
            <div class="col-12 col-md-auto d-flex align-items-center lh-1">
                <h5 class="d-inline-block fw-normal align-middle m-0">
                    Лог ошибок
                </h5>
            </div>

            <div class="col-auto d-flex align-items-center">
                <div class="spinner-border text-secondary d-none" role="status" id="preloader">
                    <span class="sr-only">Загрузка...</span>
                </div>
            </div>

            <div class="col-12 col-md d-flex flex-column flex-md-row justify-content-end mt-3 mt-md-0">
                @if($current_file)
                    <a href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}" class="text-decoration-none">
                        <span class="fa fa-download"></span> Скачать файл
                    </a>
                    <a id="clean-log" href="?clean={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}" class="text-decoration-none ms-md-4">
                        <span class="fa fa-sync"></span> Очистить файл
                    </a>
                    <a id="delete-log" href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}" class="text-decoration-none ms-md-4">
                        <span class="fa fa-trash"></span> Удалить файл
                    </a>

                    @if(count($files) > 1)
                        <a id="delete-all-log" href="?delall=true{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}" class="text-decoration-none ms-md-4">
                            <span class="fa fa-trash-alt"></span> Удалить все файлы
                        </a>
                    @endif
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col sidebar mb-3">
                <div class="list-group div-scroll">
                    @foreach($folders as $folder)
                        <div class="list-group-item">
                            <a href="?f={{ \Illuminate\Support\Facades\Crypt::encrypt($folder) }}">
                                <span class="fa fa-folder"></span> {{ $folder }}
                            </a>

                            @if ($current_folder == $folder)
                                <div class="list-group folder">
                                    @foreach($folder_files as $file)
                                        <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}&f={{ \Illuminate\Support\Facades\Crypt::encrypt($folder) }}"
                                           class="list-group-item @if ($current_file == $file) llv-active @endif">
                                            {{ $file }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @foreach($files as $file)
                        <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}"
                           class="list-group-item @if ($current_file == $file) llv-active @endif">
                            {{ $file }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="{{ count($logs) > 0 ? 'col-12 col-md-10' : 'col-md-12' }} table-container">
                @if ($logs === null)
                    <div>
                        Файл лога весит более 50 мб, пожалуйста загрузите файл вручную.
                    </div>
                @else
                    <div class="table-responsive-sm">
                        <table id="table-log" class="table table-striped table-hover align-middle" data-ordering-index="{{ $standardFormat ? 2 : 0 }}">
                            <thead>
                                <tr>
                                    @if ($standardFormat)
                                        <th>Статус</th>
                                        <th>Контекст</th>
                                        <th>Дата</th>
                                    @else
                                        <th>#</th>
                                    @endif
                                    <th style="min-width: 200px">Текст</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($logs as $key => $log)
                                <tr data-display="stack{{ $key }}">
                                    @if ($standardFormat)
                                        @php
                                            $russian = [
                                                'debug' => 'Дебаг',
                                                'info' => 'Инфо',
                                                'notice' => 'Уведомление',
                                                'warning' => 'Внимание',
                                                'error' => 'Ошибка',
                                                'critical' => 'Критично',
                                                'alert' => 'Тревога',
                                                'emergency' => 'Экстренный',
                                                'processed' => 'Обработано',
                                                'failed' => 'Провалено',
                                            ];
                                        @endphp

                                        <td class="nowrap text-{{ $log['level_class'] }}">
                                            <span class="fa fa-{{ $log['level_img'] }}" aria-hidden="true"></span>
                                            <span class="fw-bold">
                                                &nbsp;&nbsp;{{ $russian[$log['level']] }}
                                            </span>
                                        </td>
                                        <td class="text text-center">{{ $log['context'] }}</td>
                                    @endif
                                    <td class="date">{{ $log['date'] }}</td>
                                    <td class="text">
                                        @if ($log['stack'])
                                            <button type="button"
                                                    class="float-right expand btn btn-outline-dark btn-sm mb-2 ml-2"
                                                    data-display="stack{{ $key }}">
                                                <span class="fa fa-search"></span>
                                            </button>
                                        @endif

                                        {{ $log['text'] }}

                                        @if (isset($log['in_file']))
                                            <br/>{{ $log['in_file'] }}
                                        @endif

                                        @if ($log['stack'])
                                            <div class="stack" id="stack{{ $key }}"
                                                 style="display: none; white-space: pre-wrap;">{{ trim($log['stack']) }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="d-flex flex-column d-md-block py-3">
                    @if($current_file)
                        <a href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}" class="text-decoration-none">
                            <span class="fa fa-download"></span> Скачать файл
                        </a>
                        <a id="clean-log" href="?clean={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}" class="text-decoration-none ms-md-4">
                            <span class="fa fa-sync"></span> Очистить файл
                        </a>
                        <a id="delete-log" href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}" class="text-decoration-none ms-md-4">
                            <span class="fa fa-trash"></span> Удалить файл
                        </a>

                        @if(count($files) > 1)
                            <a id="delete-all-log" href="?delall=true{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}" class="text-decoration-none ms-md-4">
                                <span class="fa fa-trash-alt"></span> Удалить все файлы
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.table-container tr').on('click', function () {
                $('#' + $(this).data('display')).toggle();
            });

            $('#table-log').DataTable({
                "order": [$('#table-log').data('orderingIndex'), 'desc'],
                "stateSave": true,
                "stateSaveCallback": function (settings, data) {
                    window.localStorage.setItem("datatable", JSON.stringify(data));
                },
                "stateLoadCallback": function (settings) {
                    let data = JSON.parse(window.localStorage.getItem("datatable"));
                    if (data) data.start = 0;
                    return data;
                },
                "language": {
                    "processing": "Подождите...",
                    "search": "",
                    "searchPlaceholder": "Поиск",
                    "lengthMenu": "_MENU_",
                    "info": "Показано с _START_ до _END_ (всего: _TOTAL_)",
                    "infoEmpty": "Показано с 0 до 0 (всего: 0)",
                    "infoFiltered": "(отфильтровано из _MAX_ записей)",
                    "loadingRecords": "Загрузка записей...",
                    "zeroRecords": "Записи отсутствуют.",
                    "emptyTable": "В таблице отсутствуют данные",
                    "paginate": {
                        "first": "Первая",
                        "previous": "<",
                        "next": ">",
                        "last": "Последняя"
                    },
                    "aria": {
                        "sortAscending": ": активировать для сортировки столбца по возрастанию",
                        "sortDescending": ": активировать для сортировки столбца по убыванию"
                    },
                    "select": {
                        "rows": {
                            "_": "Выбрано записей: %d",
                            "0": "Кликните по записи для выбора",
                            "1": "Выбрана одна запись"
                        }
                    }
                }
            });

            $('#delete-log, #clean-log, #delete-all-log').click(function () {
                return confirm('Are you sure?');
            });
        });
    </script>
</x-app-layout>
