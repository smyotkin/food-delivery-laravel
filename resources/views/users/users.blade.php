<x-app-layout>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Пользователи</h5>
    </x-slot>

    <div class="container-fluid px-5 mb-5">
        <div class="row">
            <div class="col-5 mt-4">
                <input type="text" id="name-search" class="form-control rounded-0" placeholder="Поиск по номеру телефона или фамилии" aria-label="Поиск по номеру телефона или фамилии">
            </div>
            <div class="col-auto mt-4 d-flex align-items-center">
                <div class="spinner-border text-secondary d-none" role="status" id="preloader">
                    <span class="sr-only">Loading...</span>
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
        <div class="row">
            <div class="col">
                <table class="table table-striped" id="users_json">
                    <tbody>
                        <tr class="fw-light bg-light table-header">
                            <td class="border-0">Имя</td>
                            <td class="border-0">Телефон</td>
                            <td class="border-0">Должность</td>
                            <td class="border-0">Регистрация</td>
                            <td class="border-0">Страница</tdv>
                            <td class="border-0">Онлайн</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            showUsersList();
            usersUpdateInterval = setInterval(showUsersList, 10000);
        });

        function showUsersList() {
            $.ajax({
                type: 'GET',
                url: '{{ route('users/get') }}',
                // data: { get_param: 'value' },
                dataType: 'json',
                beforeSend: function (data) {
                    $('#preloader').removeClass('d-none');
                },
                complete: function() {
                    $('#preloader').addClass('d-none');
                },
                success: function (data) {
                    $('#users_json tr:not(.table-header)').remove();

                    $.each(data, function(index, user) {
                        $('#users_json').append(
                            $('<tr>', {
                                class: user.online == 'online' ? 'fw-bold' : ''
                            }).append(
                                $('<td>').append(
                                    $('<a>', {
                                        text: user.full_name,
                                        href: 'users/' + user.id,
                                        class: 'text-decoration-none'
                                    })
                                ),
                                $('<td>', {
                                    text: user.phone_formatted
                                }),
                                $('<td>', {
                                    text: '-'
                                }),
                                $('<td>', {
                                    text: user.registered_at
                                }),
                                $('<td>', {
                                    text: user.last_page
                                }),
                                $('<td>', {
                                    text: user.online,
                                    class: user.online == 'online' ? 'text-success' : ''
                                })
                            )
                        );
                    });
                }
            });
        }
    </script>
</x-app-layout>
