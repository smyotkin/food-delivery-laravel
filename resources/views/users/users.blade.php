<x-app-layout>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Пользователи</h5>
    </x-slot>

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
                <h5 class="d-inline-block fw-normal align-middle m-0">Пользователи <a href="javascript:" class="btn btn-sm btn-danger align-bottom rounded-0 px-1 py-0 ms-2"><small>CSV</small></a></h5>
            </div>
            <div class="col text-end">
                <a href="{{ route('users/add') }}" class="btn btn-outline-primary py-0">Новый пользователь</a>
            </div>
        </div>

        <div id="users_ajax">
        </div>
    </div>

    <script>
        let userSearch = $('#phone_lastname-search');
        let userTable = $('#users_ajax');
        let ajaxSearchDelay = 300;
        let usersUpdateDelay = 10000;
        let getSearchCookie = getCookie('users_query_str');
        let page = 1;

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
                // userTable.attr('data-page', '1');
                document.cookie = 'users_query_str=' + encodeURIComponent($(this).val());
                if ($(this).val().length == 0 || $(this).val().length > 1)
                    setTimeout(showUsersList, ajaxSearchDelay);
            });

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                page = $(this).attr('href').split('page=')[1];

                showUsersList(page);
            });
        });

        function showUsersListInterval() {
            if ($('#phone_lastname-search').val().length == 0 && page == 1) //  && $('#users_ajax').attr('data-page') == '1'
                showUsersList();
        }

        function showUsersList(page = 1) {
            $.ajax({
                type: 'GET',
                data: {
                    query: $('#phone_lastname-search').val(),
                },
                url: '{{ route('users/getAJAX') }}?page=' + page,
                beforeSend: function () {
                    $('#preloader').removeClass('d-none');
                },
                complete: function() {
                    $('#preloader').addClass('d-none');
                },
                success: function (data) {
                    $('#users_ajax').html(data);
                }
            });
        }
    </script>
</x-app-layout>
