<x-app-layout>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Пользователи</h5>
    </x-slot>

    @include('layouts.sub-navigation')

    <div class="container-fluid px-5 mb-5">
        <div class="row">
            <div class="col-5 mt-4">
                <input type="text" id="phone_lastname-search" class="form-control rounded-0" placeholder="Поиск по номеру телефона или фамилии" aria-label="Поиск по номеру телефона или фамилии">
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
                    Пользователи
                    @permission('users_download')
                        <a href="{{ '/users/export.csv' }}" class="btn btn-sm btn-danger align-bottom rounded-0 px-1 py-0 ms-2" id="download_csv"><small>CSV</small></a>
                    @endpermission
                </h5>
            </div>
            <div class="col text-end">
                @anyPermission('users_employee_add|users_specialist_add|users_head_add|users_owner_add')
                    <a href="{{ route('users.create') }}" class="btn btn-outline-primary py-0">Новый пользователь</a>
                @endanyPermission
            </div>
        </div>

        <div class="row">
            <div class="col">
                <table class="table table-striped">
                    <thead>
                        <tr class="fw-light bg-lightgray table-header">
                            <td class="border-0">Имя</td>
                            <td class="border-0">Телефон</td>
                            <td class="border-0">Статус</td>
                            <td class="border-0">Должность</td>
                            <td class="border-0">Регистрация</td>
                            <td class="border-0">Страница</td>
                            <td class="border-0">Онлайн</td>
                        </tr>
                    </thead>
                    <tbody id="users_ajax"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let userSearch = $('#phone_lastname-search');
        let usersBlock = $('#users_ajax');
        let usersTable = $('#users_ajax > .row');
        let ajaxSearchDelay = 300;
        let usersUpdateDelay = 10000;
        let getSearchCookie = getCookie('users_query_str');
        let page = 1;
        let searchNow = false;

        $(document).ready(function() {
            if (getSearchCookie) {
                userSearch.val(getSearchCookie);
                if ($(this).val().length == 0 || $(this).val().length > 1)
                    setTimeout(showUsersList, ajaxSearchDelay);
            } else {
                showUsersList();
                setInterval(showUsersListInterval, usersUpdateDelay);
            }

            userSearch.on('keyup', function () {
                document.cookie = 'users_query_str=' + encodeURIComponent($(this).val());
                if ($(this).val().length >= 0) {
                    searchNow = true;
                    setTimeout(showUsersList, ajaxSearchDelay);
                } else {
                    searchNow = false;
                }
            });

            $(document).on('click', '.table_pagination a', function(event) {
                event.preventDefault();

                searchNow = false;
                page = $(this).attr('href').split('page=')[1];

                showUsersList(page);
            });
        });

        function showUsersListInterval() {
            if ($('#phone_lastname-search').val().length == 0 && !searchNow && page == 1)
                showUsersList();
        }

        function showUsersList(page = 1) {
            let query = $('#phone_lastname-search').val();

            $('#download_csv').attr('href', '{{ '/users/export.csv' }}' + '?query=' + query);

            $.ajax({
                type: 'GET',
                data: {
                    query: query,
                },
                url: '{{ route('users/getAjax') }}?page=' + page,
                beforeSend: function () {
                    $('#preloader').removeClass('d-none');
                },
                complete: function() {
                    $('#preloader').addClass('d-none');
                },
                success: function (data) {
                    $('#users_ajax .table_pagination').remove();

                    if (searchNow || page == 1) {
                        $('#users_ajax').html(data);
                    } else {
                        $('#users_ajax').append(data);
                    }
                }
            });
        }
    </script>
</x-app-layout>
