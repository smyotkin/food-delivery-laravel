<x-app-layout>
    <x-slot name="title">Должности</x-slot>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Должности</h5>
    </x-slot>

    @include('layouts.sub-navigation')

    <div class="container-fluid px-4 px-md-5 mb-5">
        <div class="row">
            <div class="col-12 col-md-5 mt-4">
                <input type="text" id="name_slug-search" class="form-control rounded-0" placeholder="Поиск по названию или метке" aria-label="Поиск по названию или метке">
            </div>
            <div class="col-auto mt-4 d-flex align-items-center">
                <div class="spinner-border text-secondary d-none" role="status" id="preloader">
                    <span class="sr-only">Загрузка...</span>
                </div>
            </div>
        </div>

        <div class="row mb-3 mt-md-5">
            <div class="col-12 col-md-auto lh-1">
                <h5 class="d-inline-block fw-normal align-middle m-0">Должности</h5>
            </div>

            @permission('users_position_create')
                <div class="col-12 col-md text-end">
                    <a href="{{ route('positions.create') }}" class="d-block d-md-inline-block btn btn-outline-primary py-1 py-md-0 mt-3 mt-md-0">Новая должность</a>
                </div>
            @endpermission
        </div>

        <div class="row">
            <div class="col">
                <div class="table-responsive-sm">
                    <table class="table table-striped">
                        <thead>
                            <tr class="fw-light bg-lightgray table-header">
                                <td class="border-0">Название</td>
                                <td class="border-0">Метка</td>
                                <td class="border-0">Статус</td>
                            </tr>
                        </thead>
                        <tbody id="positions_ajax"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        let userSearch = $('#name_slug-search');
        let usersBlock = $('#positions_ajax');
        let usersTable = $('#positions_ajax > .row');
        let ajaxSearchDelay = 300;
        let usersUpdateDelay = 10000;
        let getSearchCookie = getCookie('positions_query_str');
        let page = 1;
        let searchNow = false;

        $(document).ready(function() {
            if (getSearchCookie) {
                userSearch.val(getSearchCookie);
            }

            showPositions();

            userSearch.on('keyup', function () {
                document.cookie = 'positions_query_str=' + encodeURIComponent($(this).val());

                if ($(this).val().length >= 0) {
                    searchNow = true;

                    showPositions();
                } else {
                    searchNow = false;
                }
            });

            $(document).on('click', '.table_pagination a', function(event) {
                event.preventDefault();

                searchNow = false;
                page = $(this).attr('href').split('page=')[1];

                showPositions(page);
            });
        });

        function showPositions(page = 1) {
            $.ajax({
                type: 'GET',
                data: {
                    page: page,
                    query: userSearch.val(),
                },
                url: '{{ route('positions.getAjax') }}',
                beforeSend: function () {
                    $('#preloader').removeClass('d-none');
                },
                complete: function() {
                    $('#preloader').addClass('d-none');
                },
                success: function (data) {
                    usersBlock.find('.table_pagination').remove();

                    if (searchNow || page == 1) {
                        usersBlock.html(data);
                    } else {
                        usersBlock.append(data);
                    }
                }
            });
        }
    </script>
</x-app-layout>
