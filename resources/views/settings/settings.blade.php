<x-app-layout>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Настройки</h5>
    </x-slot>

    <div class="container-fluid px-5 mb-5">
        <div class="row">
            <div class="col-5 mt-4">
                <input type="text" id="name_value-search" class="form-control rounded-0" placeholder="Поиск по названию или значению" aria-label="Поиск по названию или значению">
            </div>
            <div class="col-auto mt-4 d-flex align-items-center">
                <div class="spinner-border text-secondary d-none" role="status" id="preloader">
                    <span class="sr-only">Загрузка...</span>
                </div>
            </div>
        </div>
        <div class="row mt-5 mb-3">
            <div class="col-6 lh-1">
                <h5 class="d-inline-block fw-normal align-middle m-0">
                    Настройки
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <table class="table table-striped">
                    <thead>
                        <tr class="fw-light bg-lightgray table-header">
                            <td class="border-0">Имя</td>
                            <td class="border-0">Значение</td>
                        </tr>
                    </thead>
                    <tbody id="settings_ajax"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let userSearch = $('#name_value-search');
        let usersBlock = $('#settings_ajax');
        let usersTable = $('#settings_ajax > .row');
        let ajaxSearchDelay = 300;
        let usersUpdateDelay = 10000;
        let getSearchCookie = getCookie('settings_query_str');
        let page = 1;
        let searchNow = false;

        $(document).ready(function() {
            if (getSearchCookie) {
                userSearch.val(getSearchCookie);
                if ($(this).val().length === 0 || $(this).val().length > 1)
                    setTimeout(showUsersList, ajaxSearchDelay);
            } else {
                showUsersList();
                setInterval(showUsersListInterval, usersUpdateDelay);
            }

            userSearch.on('keyup', function () {
                document.cookie = 'settings_query_str=' + encodeURIComponent($(this).val());
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
            if ($('#name_value-search').val().length === 0 && !searchNow && page === 1)
                showUsersList();
        }

        function showUsersList(page = 1) {
            $.ajax({
                type: 'GET',
                data: {
                    query: $('#name_value-search').val(),
                },
                url: '{{ route('settings/get.ajax') }}?page=' + page,
                beforeSend: function () {
                    $('#preloader').removeClass('d-none');
                },
                complete: function() {
                    $('#preloader').addClass('d-none');
                },
                success: function (data) {
                    $('#settings_ajax .table_pagination').remove();

                    if (searchNow || page == 1) {
                        $('#settings_ajax').html(data);
                    } else {
                        $('#settings_ajax').append(data);
                    }
                }
            });
        }
    </script>
</x-app-layout>
